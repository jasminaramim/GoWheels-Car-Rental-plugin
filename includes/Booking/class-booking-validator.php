<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Booking_Validator {

	/** @return array|WP_Error  [pickup_utc, return_utc] */
	public static function parse_dates( $pickup, $return, $check_rules = true ) {
		$p = GoWheels_Util::to_utc( $pickup );
		$r = GoWheels_Util::to_utc( $return );
		if ( ! $p || ! $r ) {
			return new WP_Error( 'gowheels_dates', __( 'Please provide a valid pickup and return date and time.', 'gowheels' ), array( 'status' => 400 ) );
		}
		$pt = GoWheels_Util::ts( $p );
		$rt = GoWheels_Util::ts( $r );
		if ( $rt <= $pt ) {
			return new WP_Error( 'gowheels_dates', __( 'Return time must be after pickup time.', 'gowheels' ), array( 'status' => 400 ) );
		}
		if ( $check_rules ) {
			$s = GoWheels_Settings::all();
			if ( empty( $s['allow_past'] ) && $pt < time() + (int) $s['min_lead_hours'] * HOUR_IN_SECONDS ) {
				return new WP_Error(
					'gowheels_lead',
					(int) $s['min_lead_hours'] > 0
						/* translators: %d hours */
						? sprintf( __( 'Pickup must be at least %d hours from now.', 'gowheels' ), (int) $s['min_lead_hours'] )
						: __( 'Pickup cannot be in the past.', 'gowheels' ),
					array( 'status' => 400 )
				);
			}
			if ( (int) $s['max_rental_days'] > 0 && ( $rt - $pt ) > (int) $s['max_rental_days'] * DAY_IN_SECONDS ) {
				return new WP_Error(
					'gowheels_max',
					/* translators: %d days */
					sprintf( __( 'Maximum rental length is %d days.', 'gowheels' ), (int) $s['max_rental_days'] ),
					array( 'status' => 400 )
				);
			}
		}
		return array( 'pickup_utc' => $p, 'return_utc' => $r );
	}

	/**
	 * Validate vehicle, dates, locations and extras.
	 *
	 * @return array|WP_Error
	 */
	public static function validate_trip( $in, $need_locations = true ) {
		$vehicle_id = isset( $in['vehicle_id'] ) ? absint( $in['vehicle_id'] ) : 0;
		if ( ! $vehicle_id || GoWheels_Vehicle_CPT::TYPE !== get_post_type( $vehicle_id ) || 'publish' !== get_post_status( $vehicle_id ) ) {
			return new WP_Error( 'gowheels_vehicle', __( 'Please choose a valid vehicle.', 'gowheels' ), array( 'status' => 400 ) );
		}
		$dates = self::parse_dates(
			isset( $in['pickup'] ) ? (string) $in['pickup'] : '',
			isset( $in['return'] ) ? (string) $in['return'] : ''
		);
		if ( is_wp_error( $dates ) ) {
			return $dates;
		}

		$locs = GoWheels_Util::locations();
		$pl   = isset( $in['pickup_location'] ) ? absint( $in['pickup_location'] ) : 0;
		$rl   = isset( $in['return_location'] ) ? absint( $in['return_location'] ) : 0;
		if ( $locs && $need_locations ) {
			if ( ! isset( $locs[ $pl ] ) ) {
				return new WP_Error( 'gowheels_location', __( 'Please choose a pickup location.', 'gowheels' ), array( 'status' => 400 ) );
			}
			if ( ! $rl ) {
				$rl = $pl;
			}
			if ( ! isset( $locs[ $rl ] ) ) {
				return new WP_Error( 'gowheels_location', __( 'Please choose a return location.', 'gowheels' ), array( 'status' => 400 ) );
			}
		} else {
			$pl = isset( $locs[ $pl ] ) ? $pl : 0;
			$rl = isset( $locs[ $rl ] ) ? $rl : $pl;
		}
		$home = (int) get_post_meta( $vehicle_id, '_gw_location_id', true );
		if ( $pl && $home && $home !== $pl ) {
			return new WP_Error( 'gowheels_location', __( 'This vehicle is not available at the chosen pickup location.', 'gowheels' ), array( 'status' => 400 ) );
		}

		$extras = array();
		if ( isset( $in['extras'] ) ) {
			$raw = is_array( $in['extras'] ) ? $in['extras'] : explode( ',', (string) $in['extras'] );
			foreach ( $raw as $k ) {
				$k = sanitize_key( $k );
				if ( '' !== $k ) {
					$extras[] = $k;
				}
			}
		}

		return array(
			'vehicle_id'         => $vehicle_id,
			'pickup_utc'         => $dates['pickup_utc'],
			'return_utc'         => $dates['return_utc'],
			'pickup_location_id' => $pl,
			'return_location_id' => $rl,
			'extras'             => $extras,
		);
	}

	/** @return array|WP_Error */
	public static function validate_customer( $in ) {
		$name  = isset( $in['name'] ) ? trim( sanitize_text_field( (string) $in['name'] ) ) : '';
		$email = isset( $in['email'] ) ? sanitize_email( (string) $in['email'] ) : '';
		$phone = isset( $in['phone'] ) ? trim( sanitize_text_field( (string) $in['phone'] ) ) : '';
		$notes = isset( $in['notes'] ) ? sanitize_textarea_field( (string) $in['notes'] ) : '';
		$pref  = isset( $in['payment_preference'] ) ? sanitize_key( $in['payment_preference'] ) : 'cash';

		if ( strlen( $name ) < 2 || strlen( $name ) > 100 ) {
			return new WP_Error( 'gowheels_name', __( 'Please enter your full name.', 'gowheels' ), array( 'status' => 400 ) );
		}
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'gowheels_email', __( 'Please enter a valid email address.', 'gowheels' ), array( 'status' => 400 ) );
		}
		if ( ! preg_match( '/^[0-9+\-\s()]{6,25}$/', $phone ) ) {
			return new WP_Error( 'gowheels_phone', __( 'Please enter a valid phone number.', 'gowheels' ), array( 'status' => 400 ) );
		}
		if ( 'online' !== $pref || ! GoWheels_Payments::online_available() ) {
			$pref = 'cash';
		}
		return array(
			'name'               => $name,
			'email'              => $email,
			'phone'              => $phone,
			'notes'              => function_exists( 'mb_substr' ) ? mb_substr( $notes, 0, 1000 ) : substr( $notes, 0, 1000 ),
			'payment_preference' => $pref,
		);
	}
}
