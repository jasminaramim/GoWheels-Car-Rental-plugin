<?php
defined( 'ABSPATH' ) || exit;

/**
 * Server-side pricing. Frontend prices are never trusted.
 *
 * Subtotal   = rental + extras
 * Before tax = subtotal - discount
 * Grand      = before tax + tax
 */
class GoWheels_Pricing {

	/** Parse the "extras" setting into [key => def]. */
	public static function extras() {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) GoWheels_Settings::get( 'extras' ) ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line ) );
			if ( count( $parts ) < 2 || '' === $parts[0] || ! is_numeric( $parts[1] ) ) {
				continue;
			}
			$key         = sanitize_title( $parts[0] );
			$out[ $key ] = array(
				'key'   => $key,
				'name'  => $parts[0],
				'price' => (float) $parts[1],
				'type'  => ( isset( $parts[2] ) && 'day' === strtolower( $parts[2] ) ) ? 'day' : 'flat',
			);
		}
		return $out;
	}

	/**
	 * @return array|WP_Error
	 */
	public static function quote( $vehicle_id, $pickup_utc, $return_utc, $extra_keys = array() ) {
		$daily  = (float) get_post_meta( $vehicle_id, '_gw_price_daily', true );
		$hourly = (float) get_post_meta( $vehicle_id, '_gw_price_hourly', true );
		if ( $daily <= 0 ) {
			return new WP_Error( 'gowheels_no_price', __( 'This vehicle has no price configured.', 'gowheels' ), array( 'status' => 400 ) );
		}

		$s       = GoWheels_Settings::all();
		$seconds = GoWheels_Util::ts( $return_utc ) - GoWheels_Util::ts( $pickup_utc );
		if ( $seconds <= 0 ) {
			return new WP_Error( 'gowheels_dates', __( 'Return must be after pickup.', 'gowheels' ), array( 'status' => 400 ) );
		}
		$minutes = $seconds / 60;
		$days    = max( 1, (int) ceil( max( 0, $minutes - (int) $s['grace_minutes'] ) / 1440 ) );

		$min = max( 1, (int) get_post_meta( $vehicle_id, '_gw_min_rental', true ) );
		if ( $days < $min ) {
			return new WP_Error(
				'gowheels_min_rental',
				/* translators: %d days */
				sprintf( _n( 'Minimum rental for this vehicle is %d day.', 'Minimum rental for this vehicle is %d days.', $min, 'gowheels' ), $min ),
				array( 'status' => 400 )
			);
		}

		if ( $hourly > 0 && $minutes <= 1440 ) {
			$unit   = 'hour';
			$units  = max( 1, (int) ceil( $minutes / 60 ) );
			$rate   = $hourly;
			$rental = min( $hourly * $units, $daily );
		} else {
			$unit   = 'day';
			$units  = $days;
			$rate   = $daily;
			$rental = $daily * $days;
		}
		$rental = GoWheels_Util::round( $rental );

		$lines   = array();
		$lines[] = array(
			'label'  => sprintf( '%s × %d %s', GoWheels_Util::money( $rate ), $units, 'hour' === $unit ? _n( 'hour', 'hours', $units, 'gowheels' ) : _n( 'day', 'days', $units, 'gowheels' ) ),
			'amount' => $rental,
		);

		$defs   = self::extras();
		$extras = 0.0;
		$picked = array();
		foreach ( (array) $extra_keys as $k ) {
			if ( ! isset( $defs[ $k ] ) || isset( $picked[ $k ] ) ) {
				continue;
			}
			$e            = $defs[ $k ];
			$amt          = GoWheels_Util::round( 'day' === $e['type'] ? $e['price'] * $days : $e['price'] );
			$extras      += $amt;
			$picked[ $k ] = $k;
			$lines[]      = array( 'label' => $e['name'], 'amount' => $amt );
		}

		$discount = 0.0;
		if ( 'day' === $unit && (float) $s['discount_percent'] > 0 && (int) $s['discount_min_days'] > 0 && $days >= (int) $s['discount_min_days'] ) {
			$discount = GoWheels_Util::round( $rental * (float) $s['discount_percent'] / 100 );
		}
		$discount = (float) apply_filters( 'gowheels_discount', $discount, $vehicle_id, $pickup_utc, $return_utc );
		$discount = min( max( 0, $discount ), $rental + $extras );
		if ( $discount > 0 ) {
			$lines[] = array( 'label' => __( 'Discount', 'gowheels' ), 'amount' => -$discount );
		}

		$before_tax = $rental + $extras - $discount;
		$tax        = GoWheels_Util::round( $before_tax * (float) $s['tax_percent'] / 100 );
		if ( $tax > 0 ) {
			$lines[] = array( 'label' => sprintf( /* translators: %s percent */ __( 'Tax (%s%%)', 'gowheels' ), (float) $s['tax_percent'] ), 'amount' => $tax );
		}
		$grand = GoWheels_Util::round( $before_tax + $tax );

		if ( 'fixed' === $s['deposit_mode'] ) {
			$deposit = min( (float) $s['deposit_value'], $grand );
		} else {
			$deposit = $grand * min( 100, (float) $s['deposit_value'] ) / 100;
		}
		$deposit = GoWheels_Util::round( $deposit );

		return array(
			'currency'        => $s['currency_code'],
			'unit'            => $unit,
			'units'           => $units,
			'days'            => $days,
			'rate'            => $rate,
			'rental_subtotal' => $rental,
			'extras_total'    => GoWheels_Util::round( $extras ),
			'discount_total'  => GoWheels_Util::round( $discount ),
			'tax_total'       => $tax,
			'grand_total'     => $grand,
			'deposit_due'     => $deposit,
			'balance_due'     => $grand,
			'extras'          => array_values( $picked ),
			'lines'           => $lines,
		);
	}
}
