<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Activator {

	public static function activate() {
		GoWheels_Database::install();
		GoWheels_Capabilities::add();
		GoWheels_Vehicle_CPT::register();
		self::create_pages();

		add_filter( 'cron_schedules', array( 'GoWheels_Booking', 'cron_schedules' ) ); // phpcs:ignore WordPress.WP.CronInterval
		if ( ! wp_next_scheduled( 'gowheels_expire_holds' ) ) {
			wp_schedule_event( time() + 300, 'gowheels_five_minutes', 'gowheels_expire_holds' );
		}
		flush_rewrite_rules();
	}

	private static function create_pages() {
		$pages = array(
			'search_page_id'  => array( __( 'Rent a Car', 'gowheels' ), '[gowheels_search][gowheels_cars]' ),
			'booking_page_id' => array( __( 'Book a Car', 'gowheels' ), '[gowheels_booking]' ),
			'status_page_id'  => array( __( 'Booking Status', 'gowheels' ), '[gowheels_booking_status]' ),
		);
		$update = array();
		foreach ( $pages as $key => $page ) {
			$existing = (int) GoWheels_Settings::get( $key );
			if ( $existing && 'trash' !== get_post_status( $existing ) && get_post( $existing ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'   => $page[0],
					'post_content' => $page[1],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				$update[ $key ] = (int) $id;
			}
		}
		if ( $update ) {
			GoWheels_Settings::update( $update );
		}
	}
}
