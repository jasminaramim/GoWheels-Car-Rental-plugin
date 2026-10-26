<?php
defined( 'ABSPATH' ) || exit;

/**
 * Booking creation, status transitions, holds, event log.
 */
class GoWheels_Booking {

	public static function init() {
		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) ); // phpcs:ignore WordPress.WP.CronInterval
		add_action( 'gowheels_expire_holds', array( __CLASS__, 'expire_holds' ) );
		add_action( 'init', array( __CLASS__, 'ensure_cron' ) );
	}

	public static function cron_schedules( $s ) {
		$s['gowheels_five_minutes'] = array( 'interval' => 300, 'display' => __( 'Every 5 minutes', 'gowheels' ) );
		return $s;
	}

	public static function ensure_cron() {
		if ( ! wp_next_scheduled( 'gowheels_expire_holds' ) ) {
			wp_schedule_event( time() + 300, 'gowheels_five_minutes', 'gowheels_expire_holds' );
		}
	}

	/* ---------- Lookup ---------- */

	public static function get( $id ) {
		global $wpdb;
		$t = GoWheels_Database::bookings();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE id = %d", $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function get_by_ref( $ref ) {
		global $wpdb;
		$t = GoWheels_Database::bookings();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE booking_ref = %s", sanitize_text_field( (string) $ref ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function log( $booking_id, $type, $message = '', $actor = 0 ) {
		global $wpdb;
		$wpdb->insert(
			GoWheels_Database::events(),
			array(
				'booking_id'     => (int) $booking_id,
				'event_type'     => substr( sanitize_key( $type ), 0, 40 ),
				'actor_user_id'  => (int) $actor,
				'message'        => substr( sanitize_text_field( $message ), 0, 255 ),
				'created_at_utc' => GoWheels_Util::now_utc(),
			),
			array( '%d', '%s', '%d', '%s', '%s' )
		);
	}

	private static function new_ref() {
		for ( $i = 0; $i < 10; $i++ ) {
			$ref = 'GW-' . strtoupper( wp_generate_password( 8, false, false ) );
			if ( ! self::get_by_ref( $ref ) ) {
				return $ref;
			}
		}
		return 'GW-' . strtoupper( bin2hex( random_bytes( 5 ) ) );
	}

	/* ---------- Locking helpers (per-vehicle) ---------- */

	private static function lock( $vehicle_id ) {
		global $wpdb;
		return 1 === (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 5)', 'gw_vehicle_' . (int) $vehicle_id ) );
	}

	private static function unlock( $vehicle_id ) {
		global $wpdb;
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', 'gw_vehicle_' . (int) $vehicle_id ) );
	}

	/* ---------- Create ---------- */

	/**
	 * Create a booking request. All input is re-validated and re-priced server side.
	 *
	 * @return object|WP_Error  Booking row.
	 */
	public static function create( $in ) {
		global $wpdb;

		$trip = GoWheels_Booking_Validator::validate_trip( $in );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}
		$cust = GoWheels_Booking_Validator::validate_customer( $in );
		if ( is_wp_error( $cust ) ) {
			return $cust;
		}
		$quote = GoWheels_Pricing::quote( $trip['vehicle_id'], $trip['pickup_utc'], $trip['return_utc'], $trip['extras'] );
		if ( is_wp_error( $quote ) ) {
			return $quote;
		}

		$unavailable = new WP_Error( 'gowheels_unavailable', __( 'Sorry, this vehicle is not available for the selected dates.', 'gowheels' ), array( 'status' => 409 ) );

		if ( ! self::lock( $trip['vehicle_id'] ) ) {
			return new WP_Error( 'gowheels_busy', __( 'The system is busy. Please try again in a moment.', 'gowheels' ), array( 'status' => 503 ) );
		}

		$now    = GoWheels_Util::now_utc();
		$hold   = (int) GoWheels_Settings::get( 'hold_minutes' );
		$result = null;

		$wpdb->query( 'START TRANSACTION' );
		if ( ! GoWheels_Availability::is_available( $trip['vehicle_id'], $trip['pickup_utc'], $trip['return_utc'] ) ) {
			$wpdb->query( 'ROLLBACK' );
			$result = $unavailable;
		} else {
			$ok = $wpdb->insert(
				GoWheels_Database::bookings(),
				array(
					'booking_ref'         => self::new_ref(),
					'vehicle_id'          => $trip['vehicle_id'],
					'customer_user_id'    => get_current_user_id(),
					'customer_name'       => $cust['name'],
					'customer_email'      => $cust['email'],
					'customer_phone'      => $cust['phone'],
					'customer_notes'      => $cust['notes'],
					'pickup_at_utc'       => $trip['pickup_utc'],
					'return_at_utc'       => $trip['return_utc'],
					'pickup_location_id'  => $trip['pickup_location_id'],
					'return_location_id'  => $trip['return_location_id'],
					'status'              => GoWheels_Booking_Status::PENDING,
					'payment_status'      => 'unpaid',
					'payment_preference'  => $cust['payment_preference'],
					'currency'            => $quote['currency'],
					'rental_subtotal'     => $quote['rental_subtotal'],
					'extras_total'        => $quote['extras_total'],
					'discount_total'      => $quote['discount_total'],
					'tax_total'           => $quote['tax_total'],
					'grand_total'         => $quote['grand_total'],
					'deposit_due'         => $quote['deposit_due'],
					'balance_due'         => $quote['grand_total'],
					'price_snapshot'      => wp_json_encode( $quote ),
					'hold_expires_at_utc' => gmdate( 'Y-m-d H:i:s', time() + $hold * MINUTE_IN_SECONDS ),
					'created_at_utc'      => $now,
					'updated_at_utc'      => $now,
				)
			);
			if ( ! $ok ) {
				$wpdb->query( 'ROLLBACK' );
				$result = new WP_Error( 'gowheels_db', __( 'Could not save your booking. Please try again.', 'gowheels' ), array( 'status' => 500 ) );
			} else {
				$id = (int) $wpdb->insert_id;
				self::log( $id, 'request_created', 'Booking request created', get_current_user_id() );
				$wpdb->query( 'COMMIT' );
				$result = $id;
			}
		}
		self::unlock( $trip['vehicle_id'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$booking = self::get( $result );
		GoWheels_Email::send( 'request_received', $booking );
		GoWheels_Email::send( 'admin_new_request', $booking );
		do_action( 'gowheels_booking_created', $booking );
		return $booking;
	}

	/* ---------- Transitions ---------- */

	/**
	 * @return true|WP_Error
	 */
	public static function transition( $id, $to, $actor = 0, $note = '' ) {
		global $wpdb;
		$b = self::get( $id );
		if ( ! $b ) {
			return new WP_Error( 'gowheels_not_found', __( 'Booking not found.', 'gowheels' ), array( 'status' => 404 ) );
		}
		if ( ! GoWheels_Booking_Status::can_transition( $b->status, $to ) ) {
			return new WP_Error(
				'gowheels_bad_transition',
				/* translators: 1: current status 2: target status */
				sprintf( __( 'A %1$s booking cannot be changed to %2$s.', 'gowheels' ), GoWheels_Booking_Status::label( $b->status ), GoWheels_Booking_Status::label( $to ) ),
				array( 'status' => 409 )
			);
		}

		$locked = false;
		if ( GoWheels_Booking_Status::APPROVED === $to ) {
			if ( ! self::lock( $b->vehicle_id ) ) {
				return new WP_Error( 'gowheels_busy', __( 'The system is busy. Please try again.', 'gowheels' ), array( 'status' => 503 ) );
			}
			$locked = true;
			$wpdb->query( 'START TRANSACTION' );
			if ( ! GoWheels_Availability::is_available( $b->vehicle_id, $b->pickup_at_utc, $b->return_at_utc, $b->id ) ) {
				$wpdb->query( 'ROLLBACK' );
				self::unlock( $b->vehicle_id );
				return new WP_Error( 'gowheels_unavailable', __( 'Cannot approve: the vehicle is no longer available for these dates.', 'gowheels' ), array( 'status' => 409 ) );
			}
		}

		$data = array( 'status' => $to, 'updated_at_utc' => GoWheels_Util::now_utc() );
		if ( GoWheels_Booking_Status::PENDING !== $to ) {
			$data['hold_expires_at_utc'] = null;
		}
		$ok = $wpdb->update( GoWheels_Database::bookings(), $data, array( 'id' => (int) $id ) );

		if ( $locked ) {
			$wpdb->query( false === $ok ? 'ROLLBACK' : 'COMMIT' );
			self::unlock( $b->vehicle_id );
		}
		if ( false === $ok ) {
			return new WP_Error( 'gowheels_db', __( 'Could not update the booking.', 'gowheels' ), array( 'status' => 500 ) );
		}

		self::log( $id, 'status_' . $to, trim( GoWheels_Booking_Status::label( $to ) . ( $note ? ' — ' . $note : '' ) ), $actor );

		$fresh = self::get( $id );
		$mail  = array(
			GoWheels_Booking_Status::APPROVED  => 'approved',
			GoWheels_Booking_Status::REJECTED  => 'rejected',
			GoWheels_Booking_Status::CANCELLED => 'cancelled',
			GoWheels_Booking_Status::EXPIRED   => 'expired',
		);
		if ( isset( $mail[ $to ] ) ) {
			GoWheels_Email::send( $mail[ $to ], $fresh, array( 'note' => $note ) );
			if ( GoWheels_Booking_Status::CANCELLED === $to && ! $actor ) {
				GoWheels_Email::send( 'admin_cancelled', $fresh );
			}
		}
		do_action( 'gowheels_booking_status_changed', (int) $id, $to, $b->status );
		return true;
	}

	/** Customer-initiated cancellation (already authorised via token). */
	public static function customer_cancel( $booking ) {
		if ( ! in_array( $booking->status, array( GoWheels_Booking_Status::PENDING, GoWheels_Booking_Status::EXPIRED, GoWheels_Booking_Status::APPROVED ), true ) ) {
			return new WP_Error( 'gowheels_no_cancel', __( 'This booking can no longer be cancelled online. Please contact us.', 'gowheels' ), array( 'status' => 409 ) );
		}
		if ( GoWheels_Booking_Status::EXPIRED === $booking->status ) {
			return new WP_Error( 'gowheels_no_cancel', __( 'This request has already lapsed.', 'gowheels' ), array( 'status' => 409 ) );
		}
		if ( GoWheels_Util::ts( $booking->pickup_at_utc ) <= time() ) {
			return new WP_Error( 'gowheels_no_cancel', __( 'Pickup time has passed. Please contact us.', 'gowheels' ), array( 'status' => 409 ) );
		}
		return self::transition( $booking->id, GoWheels_Booking_Status::CANCELLED, 0, 'Cancelled by customer' );
	}

	/** Admin: extend the temporary hold of a pending request. */
	public static function extend_hold( $id, $minutes, $actor = 0 ) {
		global $wpdb;
		$b = self::get( $id );
		if ( ! $b || GoWheels_Booking_Status::PENDING !== $b->status ) {
			return new WP_Error( 'gowheels_bad_transition', __( 'Only pending requests can have their hold extended.', 'gowheels' ), array( 'status' => 409 ) );
		}
		if ( ! self::lock( $b->vehicle_id ) ) {
			return new WP_Error( 'gowheels_busy', __( 'The system is busy. Please try again.', 'gowheels' ), array( 'status' => 503 ) );
		}
		$err = null;
		if ( ! GoWheels_Availability::is_available( $b->vehicle_id, $b->pickup_at_utc, $b->return_at_utc, $b->id ) ) {
			$err = new WP_Error( 'gowheels_unavailable', __( 'Cannot extend: the vehicle is already taken for these dates.', 'gowheels' ), array( 'status' => 409 ) );
		} else {
			$base = max( time(), $b->hold_expires_at_utc ? GoWheels_Util::ts( $b->hold_expires_at_utc ) : 0 );
			$new  = gmdate( 'Y-m-d H:i:s', $base + max( 1, (int) $minutes ) * MINUTE_IN_SECONDS );
			$wpdb->update( GoWheels_Database::bookings(), array( 'hold_expires_at_utc' => $new, 'updated_at_utc' => GoWheels_Util::now_utc() ), array( 'id' => (int) $id ) );
			self::log( $id, 'hold_extended', 'Hold extended until ' . $new . ' UTC', $actor );
		}
		self::unlock( $b->vehicle_id );
		return $err ? $err : true;
	}

	/** Cron: pending requests whose hold lapsed stop blocking the car. */
	public static function expire_holds() {
		global $wpdb;
		$t   = GoWheels_Database::bookings();
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$t} WHERE status = 'pending_review' AND hold_expires_at_utc IS NOT NULL AND hold_expires_at_utc < %s", GoWheels_Util::now_utc() ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		foreach ( $ids as $id ) {
			self::transition( (int) $id, GoWheels_Booking_Status::EXPIRED, 0, 'Temporary hold expired' );
		}
	}

	/* ---------- Views ---------- */

	public static function net_paid( $booking_id ) {
		return GoWheels_Payments::net_paid( $booking_id );
	}

	/** Safe data for customers (no admin notes / internal ids). */
	public static function public_view( $b ) {
		$paid        = self::net_paid( $b->id );
		$snap        = json_decode( (string) $b->price_snapshot, true );
		$active      = in_array( $b->status, array( GoWheels_Booking_Status::APPROVED, GoWheels_Booking_Status::IN_PROGRESS ), true );
		$online      = GoWheels_Payments::online_available();
		$dep_remain  = max( 0, GoWheels_Util::round( (float) $b->deposit_due - $paid ) );
		$balance     = max( 0, GoWheels_Util::round( (float) $b->grand_total - $paid ) );
		$can_cancel  = in_array( $b->status, array( GoWheels_Booking_Status::PENDING, GoWheels_Booking_Status::APPROVED ), true ) && GoWheels_Util::ts( $b->pickup_at_utc ) > time();

		return array(
			'ref'                  => $b->booking_ref,
			'status'               => $b->status,
			'status_label'         => GoWheels_Booking_Status::label( $b->status ),
			'payment_status'       => $b->payment_status,
			'payment_status_label' => GoWheels_Booking_Status::payment_label( $b->payment_status ),
			'vehicle'              => get_the_title( $b->vehicle_id ),
			'pickup'               => GoWheels_Util::to_local( $b->pickup_at_utc ),
			'return'               => GoWheels_Util::to_local( $b->return_at_utc ),
			'pickup_location'      => GoWheels_Util::location_name( $b->pickup_location_id ),
			'return_location'      => GoWheels_Util::location_name( $b->return_location_id ),
			'lines'                => isset( $snap['lines'] ) ? $snap['lines'] : array(),
			'grand_total'          => (float) $b->grand_total,
			'deposit_due'          => (float) $b->deposit_due,
			'paid'                 => $paid,
			'balance_due'          => $balance,
			'deposit_remaining'    => $dep_remain,
			'hold_expires'         => ( GoWheels_Booking_Status::PENDING === $b->status && $b->hold_expires_at_utc ) ? GoWheels_Util::to_local( $b->hold_expires_at_utc ) : '',
			'can_cancel'           => $can_cancel,
			'can_pay'              => $online && $active && $balance > 0.009,
			'can_pay_deposit'      => $online && $active && $dep_remain > 0.009,
		);
	}
}
