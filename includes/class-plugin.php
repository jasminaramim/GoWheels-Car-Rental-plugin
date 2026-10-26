<?php
defined( 'ABSPATH' ) || exit;

/**
 * Main plugin loader.
 */
class GoWheels_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function load_files() {
		$files = array(
			'class-util.php',
			'class-database.php',
			'class-capabilities.php',
			'class-activator.php',
			'class-deactivator.php',
			'Admin/class-settings.php',
			'Vehicles/class-vehicle-cpt.php',
			'Vehicles/class-vehicle-meta.php',
			'Vehicles/class-vehicle-service.php',
			'Pricing/class-pricing-engine.php',
			'Booking/class-booking-status.php',
			'Booking/class-availability.php',
			'Booking/class-booking-validator.php',
			'Booking/class-booking-service.php',
			'Notifications/class-email-service.php',
			'Payments/interface-gateway.php',
			'Payments/class-cash-gateway.php',
			'Payments/class-online-gateway.php',
			'Payments/class-payment-service.php',
			'REST/class-rest-controller.php',
			'Admin/class-booking-list.php',
			'Admin/class-admin-menu.php',
			'Frontend/class-assets.php',
			'Frontend/class-shortcodes.php',
		);
		foreach ( $files as $file ) {
			require_once GOWHEELS_PATH . 'includes/' . $file;
		}
		require_once GOWHEELS_PATH . 'integrations/elementor/class-elementor.php';
	}

	private function __construct() {
		add_action( 'init', array( $this, 'i18n' ), 1 );
		add_action( 'init', array( 'GoWheels_Database', 'maybe_upgrade' ), 5 );

		GoWheels_Vehicle_CPT::init();
		GoWheels_Vehicle_Meta::init();
		GoWheels_Booking::init();
		GoWheels_REST::init();
		GoWheels_Assets::init();
		GoWheels_Shortcodes::init();
		GoWheels_Elementor::init();

		if ( is_admin() ) {
			GoWheels_Settings::init();
			GoWheels_Admin_Menu::init();
			GoWheels_Booking_List::init();
		}
	}

	public function i18n() {
		load_plugin_textdomain( 'gowheels', false, dirname( plugin_basename( GOWHEELS_FILE ) ) . '/languages' );
	}
}
