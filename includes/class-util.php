<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shared helpers: time zones, money, tokens, templates.
 */
class GoWheels_Util {

	public static function now_utc() {
		return gmdate( 'Y-m-d H:i:s' );
	}

	public static function ts( $utc ) {
		return strtotime( $utc . ' UTC' );
	}

	/**
	 * Convert a site-local "Y-m-d H:i" (or "Y-m-dTH:i") string to a UTC MySQL datetime.
	 *
	 * @return string|false
	 */
	public static function to_utc( $local ) {
		$local = trim( str_replace( 'T', ' ', (string) $local ) );
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $local ) ) {
			return false;
		}
		try {
			$dt = new DateTimeImmutable( $local, wp_timezone() );
		} catch ( Exception $e ) {
			return false;
		}
		return $dt->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
	}

	public static function to_local( $utc, $format = null ) {
		if ( ! $utc ) {
			return '';
		}
		if ( ! $format ) {
			$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		}
		return wp_date( $format, self::ts( $utc ) );
	}

	public static function money( $amount ) {
		$s = GoWheels_Settings::all();
		return $s['currency_symbol'] . number_format_i18n( (float) $amount, (int) $s['display_decimals'] );
	}

	public static function round( $n ) {
		return round( (float) $n, 2 );
	}

	/* ---- Access tokens (booking lookup is never authorised by reference alone) ---- */

	public static function token( $ref ) {
		return substr( hash_hmac( 'sha256', (string) $ref, wp_salt( 'auth' ) ), 0, 32 );
	}

	public static function check_token( $ref, $token ) {
		return is_string( $token ) && '' !== $token && hash_equals( self::token( $ref ), $token );
	}

	public static function status_url( $booking ) {
		$page = (int) GoWheels_Settings::get( 'status_page_id' );
		$base = $page ? get_permalink( $page ) : home_url( '/' );
		return add_query_arg(
			array(
				'gw_ref'   => $booking->booking_ref,
				'gw_token' => self::token( $booking->booking_ref ),
			),
			$base
		);
	}

	public static function client_ip() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}

	/* ---- Locations (taxonomy terms) ---- */

	public static function locations() {
		$terms = get_terms( array( 'taxonomy' => GoWheels_Vehicle_CPT::TAX, 'hide_empty' => false ) );
		$out   = array();
		if ( is_wp_error( $terms ) ) {
			return $out;
		}
		foreach ( $terms as $t ) {
			$out[ (int) $t->term_id ] = $t->name;
		}
		return $out;
	}

	public static function location_name( $id ) {
		$all = self::locations();
		return isset( $all[ (int) $id ] ) ? $all[ (int) $id ] : '';
	}

	/* ---- Templates (theme override: your-theme/gowheels/{name}.php) ---- */

	public static function template( $name, $vars = array() ) {
		$theme = locate_template( 'gowheels/' . $name . '.php' );
		$file  = $theme ? $theme : GOWHEELS_PATH . 'templates/' . $name . '.php';
		if ( ! file_exists( $file ) ) {
			return '';
		}
		extract( $vars, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract
		ob_start();
		include $file;
		return ob_get_clean();
	}
}
