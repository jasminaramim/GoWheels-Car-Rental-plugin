<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class GoWheels_Cars_Widget extends GoWheels_Base_Widget {

	public function get_name() {
		return 'gowheels_cars';
	}

	public function get_title() {
		return __( 'GoWheels Vehicle Grid', 'gowheels' );
	}

	protected function content_controls() {
		$this->add_control( 'limit', array( 'label' => __( 'Number of vehicles', 'gowheels' ), 'type' => Controls_Manager::NUMBER, 'default' => 12, 'min' => 1, 'max' => 48 ) );
		$this->add_control( 'layout', array(
			'label'   => __( 'Layout Mode', 'gowheels' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'grid',
			'options' => array(
				'grid' => __( 'Standard Grid', 'gowheels' ),
				'tabs' => __( 'Interactive Category Tabs', 'gowheels' ),
			),
		) );
		$this->add_control( 'template', array(
			'label'   => __( 'Card Style', 'gowheels' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'classic',
			'options' => array(
				'classic'    => __( 'Classic Card', 'gowheels' ),
				'modern'     => __( 'Modern Badge Card', 'gowheels' ),
				'horizontal' => __( 'Horizontal List Card', 'gowheels' ),
			),
		) );
		$this->add_responsive_control( 'columns', array(
			'label'     => __( 'Columns', 'gowheels' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '3',
			'options'   => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
			'selectors' => array( '{{WRAPPER}} .gw-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr)) !important;' ),
		) );
	}

	protected function shortcode_string( array $s ) {
		$limit    = absint( isset( $s['limit'] ) ? $s['limit'] : 12 );
		$layout   = ! empty( $s['layout'] ) ? sanitize_key( $s['layout'] ) : 'grid';
		$template = ! empty( $s['template'] ) ? sanitize_key( $s['template'] ) : 'classic';
		return '[gowheels_cars limit="' . $limit . '" layout="' . $layout . '" template="' . $template . '"]';
	}
}

class GoWheels_Car_Details_Widget extends GoWheels_Base_Widget {

	public function get_name() {
		return 'gowheels_car_details';
	}

	public function get_title() {
		return __( 'GoWheels Vehicle Details', 'gowheels' );
	}

	protected function content_controls() {
		$options = array( '0' => __( 'Current vehicle page', 'gowheels' ) );
		foreach ( GoWheels_Vehicle_Service::ids() as $id ) {
			$options[ (string) $id ] = get_the_title( $id );
		}
		$this->add_control( 'vehicle_id', array( 'label' => __( 'Vehicle', 'gowheels' ), 'type' => Controls_Manager::SELECT, 'default' => '0', 'options' => $options ) );
		$this->add_control( 'template', array(
			'label'   => __( 'Template Design', 'gowheels' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'modern',
			'options' => array(
				'modern'  => __( 'Modern Split Showcase', 'gowheels' ),
				'tabs'    => __( 'Tabbed Executive Card', 'gowheels' ),
				'classic' => __( 'Luxury Classic Showcase', 'gowheels' ),
			),
		) );
	}

	protected function shortcode_string( array $s ) {
		$id       = absint( isset( $s['vehicle_id'] ) ? $s['vehicle_id'] : 0 );
		$template = ! empty( $s['template'] ) ? sanitize_key( $s['template'] ) : 'modern';
		return '[gowheels_car id="' . $id . '" template="' . $template . '"]';
	}
}
