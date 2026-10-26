<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Base widget: renders a GoWheels shortcode and exposes common style controls.
 */
abstract class GoWheels_Base_Widget extends \Elementor\Widget_Base {

	abstract protected function shortcode_string( array $settings );

	public function get_categories() {
		return array( 'gowheels' );
	}

	public function get_icon() {
		return 'eicon-car';
	}

	public function get_style_depends() {
		return array( 'gowheels' );
	}

	public function get_script_depends() {
		return array( 'gowheels' );
	}

	protected function content_controls() {}

	protected function register_controls() {
		$this->start_controls_section( 'gw_content', array( 'label' => __( 'Content', 'gowheels' ), 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->content_controls();
		$this->end_controls_section();

		$this->start_controls_section( 'gw_style', array( 'label' => __( 'Style', 'gowheels' ), 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_control( 'btn_bg', array(
			'label'     => __( 'Button color', 'gowheels' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gw-btn' => 'background-color: {{VALUE}};' ),
		) );
		$this->add_control( 'btn_color', array(
			'label'     => __( 'Button text color', 'gowheels' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .gw-btn' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'radius', array(
			'label'      => __( 'Border radius', 'gowheels' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
			'selectors'  => array( '{{WRAPPER}} .gw-btn, {{WRAPPER}} .gw-card, {{WRAPPER}} .gw-field, {{WRAPPER}} .gw-fieldset, {{WRAPPER}} .gw-search-grid' => 'border-radius: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'gap', array(
			'label'      => __( 'Spacing', 'gowheels' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
			'selectors'  => array( '{{WRAPPER}} .gw-grid, {{WRAPPER}} .gw-search-grid' => 'gap: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'typography',
			'selector' => '{{WRAPPER}} .gw-wrap',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		GoWheels_Assets::enqueue();
		echo do_shortcode( $this->shortcode_string( $this->get_settings_for_display() ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- shortcode templates escape their output.
	}
}
