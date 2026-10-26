<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Booking_Widget extends GoWheels_Base_Widget {

	public function get_name() {
		return 'gowheels_booking';
	}

	public function get_title() {
		return __( 'GoWheels Booking Form', 'gowheels' );
	}

	protected function shortcode_string( array $s ) {
		return '[gowheels_booking]';
	}
}

class GoWheels_Booking_Summary_Widget extends GoWheels_Base_Widget {

	public function get_name() {
		return 'gowheels_booking_summary';
	}

	public function get_title() {
		return __( 'GoWheels Booking Summary', 'gowheels' );
	}

	protected function shortcode_string( array $s ) {
		return '[gowheels_booking_status]';
	}
}
