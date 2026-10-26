<?php
defined( 'ABSPATH' ) || exit;

/**
 * Elementor integration. Loads only when Elementor is active; shortcodes work without it.
 */
class GoWheels_Elementor {

	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register' ) );
	}

	public static function category( $manager ) {
		$manager->add_category( 'gowheels', array( 'title' => 'GoWheels', 'icon' => 'eicon-car' ) );
	}

	public static function register( $widgets ) {
		$dir = GOWHEELS_PATH . 'integrations/elementor/widgets/';
		require_once $dir . 'class-base-widget.php';
		require_once $dir . 'class-search-widget.php';
		require_once $dir . 'class-cars-widget.php';
		require_once $dir . 'class-booking-widget.php';

		$widgets->register( new GoWheels_Search_Widget() );
		$widgets->register( new GoWheels_Cars_Widget() );
		$widgets->register( new GoWheels_Car_Details_Widget() );
		$widgets->register( new GoWheels_Booking_Widget() );
		$widgets->register( new GoWheels_Booking_Summary_Widget() );
	}
}
