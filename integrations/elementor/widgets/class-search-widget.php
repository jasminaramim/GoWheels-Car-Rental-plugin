<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Search_Widget extends GoWheels_Base_Widget {

	public function get_name() {
		return 'gowheels_search';
	}

	public function get_title() {
		return __( 'GoWheels Search Form', 'gowheels' );
	}

	protected function shortcode_string( array $s ) {
		return '[gowheels_search]';
	}
}
