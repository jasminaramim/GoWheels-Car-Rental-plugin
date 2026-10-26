<?php
defined( 'ABSPATH' ) || exit;

/**
 * REST API: /wp-json/gowheels/v1/
 */
class GoWheels_REST {

	const NS = 'gowheels/v1';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function can_manage() {
		return current_user_can( GoWheels_Capabilities::MANAGE );
	}

	public static function routes() {
		$pub = '__return_true';
		$r   = function ( $path, $methods, $cb, $perm, $args = array() ) {
			register_rest_route(
				self::NS,
				$path,
				array(
					'methods'             => $methods,
					'callback'            => array( __CLASS__, $cb ),
					'permission_callback' => $perm,
					'args'                => $args,
				)
			);
		};

		$r( '/vehicles', 'GET', 'vehicles', $pub );
		$r( '/vehicles/(?P<id>\d+)', 'GET', 'vehicle', $pub );
		$r( '/availability', 'GET', 'availability', $pub );
		$r( '/quote', 'GET', 'quote', $pub );
		$r( '/bookings', 'POST', 'create_booking', $pub ); // Public by design: validated, rate limited.
		$r( '/bookings/(?P<ref>[A-Za-z0-9\-]+)', 'GET', 'get_booking', $pub ); // Token-authorised inside.
		$r( '/bookings/(?P<ref>[A-Za-z0-9\-]+)/cancel', 'POST', 'cancel_booking', $pub );
		$r( '/bookings/(?P<ref>[A-Za-z0-9\-]+)/payment', 'POST', 'start_payment', $pub );
		$r( '/payments/webhook/(?P<gateway>[a-z_]+)', 'POST', 'webhook', $pub ); // Signature-verified inside.

		$admin = array( __CLASS__, 'can_manage' );
		$r( '/admin/bookings', 'GET', 'admin_bookings', $admin );
		$r( '/admin/bookings/(?P<id>\d+)/approve', 'POST', 'admin_approve', $admin );
		$r( '/admin/bookings/(?P<id>\d+)/reject', 'POST', 'admin_reject', $admin );
	}

	/* ---------- Public ---------- */

	public static function vehicles() {
		return rest_ensure_response( GoWheels_Vehicle_Service::all() );
	}

	public static function vehicle( WP_REST_Request $req ) {
		$d = GoWheels_Vehicle_Service::data( absint( $req['id'] ) );
		return $d ? rest_ensure_response( $d ) : new WP_Error( 'gowheels_not_found', __( 'Vehicle not found.', 'gowheels' ), array( 'status' => 404 ) );
	}

	public static function availability( WP_REST_Request $req ) {
		$dates = GoWheels_Booking_Validator::parse_dates( (string) $req->get_param( 'pickup' ), (string) $req->get_param( 'return' ) );
		if ( is_wp_error( $dates ) ) {
			return $dates;
		}
		$out = array();
		foreach ( GoWheels_Availability::available_vehicle_ids( $dates['pickup_utc'], $dates['return_utc'], absint( $req->get_param( 'pickup_location' ) ) ) as $id ) {
			$q = GoWheels_Pricing::quote( $id, $dates['pickup_utc'], $dates['return_utc'] );
			if ( is_wp_error( $q ) ) {
				continue; // e.g. below the vehicle's minimum rental.
			}
			$d          = GoWheels_Vehicle_Service::data( $id );
			$d['quote'] = $q;
			$out[]      = $d;
		}
		return rest_ensure_response( $out );
	}

	public static function quote( WP_REST_Request $req ) {
		$trip = GoWheels_Booking_Validator::validate_trip( $req->get_params(), false );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}
		$q = GoWheels_Pricing::quote( $trip['vehicle_id'], $trip['pickup_utc'], $trip['return_utc'], $trip['extras'] );
		if ( is_wp_error( $q ) ) {
			return $q;
		}
		$q['available'] = GoWheels_Availability::is_available( $trip['vehicle_id'], $trip['pickup_utc'], $trip['return_utc'] );
		return rest_ensure_response( $q );
	}

	private static function rate_limited() {
		$limit = (int) apply_filters( 'gowheels_booking_rate_limit', 10 );
		$key   = 'gw_rl_' . md5( GoWheels_Util::client_ip() );
		$n     = (int) get_transient( $key );
		if ( $n >= $limit ) {
			return true;
		}
		set_transient( $key, $n + 1, HOUR_IN_SECONDS );
		return false;
	}

	public static function create_booking( WP_REST_Request $req ) {
		$in = $req->get_json_params();
		if ( ! is_array( $in ) ) {
			$in = $req->get_params();
		}
		// Honeypot: bots fill hidden fields. Pretend success.
		if ( ! empty( $in['website'] ) ) {
			return new WP_REST_Response( array( 'ok' => true ), 201 );
		}
		if ( self::rate_limited() ) {
			return new WP_Error( 'gowheels_rate_limited', __( 'Too many requests. Please try again later.', 'gowheels' ), array( 'status' => 429 ) );
		}
		$b = GoWheels_Booking::create( $in );
		if ( is_wp_error( $b ) ) {
			return $b;
		}
		return new WP_REST_Response(
			array(
				'ok'          => true,
				'ref'         => $b->booking_ref,
				'status_url'  => GoWheels_Util::status_url( $b ),
				'booking'     => GoWheels_Booking::public_view( $b ),
			),
			201
		);
	}

	/** Resolve booking via ref + token. Same error for "not found" and "bad token". */
	private static function authorised_booking( WP_REST_Request $req ) {
		$ref = (string) $req['ref'];
		$b   = GoWheels_Booking::get_by_ref( $ref );
		$tok = (string) $req->get_param( 'token' );
		if ( ! $b || ! GoWheels_Util::check_token( $b->booking_ref, $tok ) ) {
			return new WP_Error( 'gowheels_not_found', __( 'Booking not found.', 'gowheels' ), array( 'status' => 404 ) );
		}
		return $b;
	}

	public static function get_booking( WP_REST_Request $req ) {
		$b = self::authorised_booking( $req );
		return is_wp_error( $b ) ? $b : rest_ensure_response( GoWheels_Booking::public_view( $b ) );
	}

	public static function cancel_booking( WP_REST_Request $req ) {
		$b = self::authorised_booking( $req );
		if ( is_wp_error( $b ) ) {
			return $b;
		}
		$r = GoWheels_Booking::customer_cancel( $b );
		if ( is_wp_error( $r ) ) {
			return $r;
		}
		return rest_ensure_response( GoWheels_Booking::public_view( GoWheels_Booking::get( $b->id ) ) );
	}

	public static function start_payment( WP_REST_Request $req ) {
		$b = self::authorised_booking( $req );
		if ( is_wp_error( $b ) ) {
			return $b;
		}
		$r = GoWheels_Payments::start_online( $b, (string) $req->get_param( 'type' ) );
		return is_wp_error( $r ) ? $r : rest_ensure_response( $r );
	}

	public static function webhook( WP_REST_Request $req ) {
		$g = GoWheels_Payments::gateway( sanitize_key( $req['gateway'] ) );
		if ( ! $g || ! $g->is_enabled() ) {
			return new WP_Error( 'gowheels_no_gateway', __( 'Unknown gateway.', 'gowheels' ), array( 'status' => 404 ) );
		}
		return $g->handle_webhook( $req );
	}

	/* ---------- Admin ---------- */

	public static function admin_bookings( WP_REST_Request $req ) {
		global $wpdb;
		$t      = GoWheels_Database::bookings();
		$status = sanitize_key( (string) $req->get_param( 'status' ) );
		$page   = max( 1, absint( $req->get_param( 'page' ) ) );
		$per    = min( 100, max( 1, absint( $req->get_param( 'per_page' ) ? $req->get_param( 'per_page' ) : 20 ) ) );
		$where  = isset( GoWheels_Booking_Status::labels()[ $status ] ) ? $wpdb->prepare( 'WHERE status = %s', $status ) : '';
		$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} {$where} ORDER BY id DESC LIMIT %d OFFSET %d", $per, ( $page - 1 ) * $per ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$out    = array();
		foreach ( $rows as $b ) {
			$out[] = array_merge(
				GoWheels_Booking::public_view( $b ),
				array(
					'id'       => (int) $b->id,
					'customer' => array( 'name' => $b->customer_name, 'email' => $b->customer_email, 'phone' => $b->customer_phone ),
				)
			);
		}
		return rest_ensure_response( $out );
	}

	public static function admin_approve( WP_REST_Request $req ) {
		$r = GoWheels_Booking::transition( absint( $req['id'] ), GoWheels_Booking_Status::APPROVED, get_current_user_id() );
		return is_wp_error( $r ) ? $r : rest_ensure_response( array( 'ok' => true ) );
	}

	public static function admin_reject( WP_REST_Request $req ) {
		$r = GoWheels_Booking::transition( absint( $req['id'] ), GoWheels_Booking_Status::REJECTED, get_current_user_id(), sanitize_text_field( (string) $req->get_param( 'note' ) ) );
		return is_wp_error( $r ) ? $r : rest_ensure_response( array( 'ok' => true ) );
	}
}
