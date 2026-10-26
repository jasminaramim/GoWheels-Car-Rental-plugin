<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Assets {

	private static $enqueued = false;

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ) );
	}

	public static function register() {
		wp_register_style( 'gowheels', GOWHEELS_URL . 'assets/css/frontend.css', array(), GOWHEELS_VERSION );
		wp_register_script( 'gowheels', GOWHEELS_URL . 'assets/js/frontend.js', array(), GOWHEELS_VERSION, true );
	}

	/** Called by shortcodes/templates so assets load only where needed. */
	public static function enqueue() {
		if ( self::$enqueued ) {
			return;
		}
		self::$enqueued = true;
		if ( ! wp_script_is( 'gowheels', 'registered' ) ) {
			self::register();
		}
		wp_enqueue_style( 'gowheels' );
		wp_enqueue_script( 'gowheels' );
		$s = GoWheels_Settings::all();
		wp_localize_script(
			'gowheels',
			'gowheelsData',
			array(
				'rest'     => esc_url_raw( rest_url( GoWheels_REST::NS . '/' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'symbol'   => $s['currency_symbol'],
				'decimals' => (int) $s['display_decimals'],
				'i18n'     => array(
					'error'      => __( 'Something went wrong. Please try again.', 'gowheels' ),
					'sending'    => __( 'Sending…', 'gowheels' ),
					'submit'     => __( 'Send booking request', 'gowheels' ),
					'unavail'    => __( 'This vehicle is not available for the selected dates.', 'gowheels' ),
					'total'      => __( 'Estimated total', 'gowheels' ),
					'deposit'    => __( 'Deposit if approved', 'gowheels' ),
					'confirm'    => __( 'Cancel this booking?', 'gowheels' ),
					'redirect'   => __( 'Redirecting to secure payment…', 'gowheels' ),
				),
			)
		);
	}
}
