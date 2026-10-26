<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Vehicle_CPT {

	const TYPE = 'gowheels_vehicle';
	const TAX  = 'gowheels_location';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_filter( 'the_content', array( __CLASS__, 'single_content' ) );
	}

	public static function register() {
		register_post_type(
			self::TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Vehicles', 'gowheels' ),
					'singular_name' => __( 'Vehicle', 'gowheels' ),
					'add_new_item'  => __( 'Add New Vehicle', 'gowheels' ),
					'edit_item'     => __( 'Edit Vehicle', 'gowheels' ),
					'all_items'     => __( 'All Vehicles', 'gowheels' ),
				),
				'public'          => true,
				'show_in_menu'    => false,
				'show_in_rest'    => false,
				'has_archive'     => false,
				'rewrite'         => array( 'slug' => 'vehicle' ),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'capability_type' => array( 'gowheels_vehicle', 'gowheels_vehicles' ),
				'map_meta_cap'    => true,
			)
		);

		register_taxonomy(
			self::TAX,
			self::TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Locations', 'gowheels' ),
					'singular_name' => __( 'Location', 'gowheels' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => false,
				'hierarchical'      => false,
				'meta_box_cb'       => false,
				'rewrite'           => false,
				'capabilities'      => array(
					'manage_terms' => GoWheels_Capabilities::MANAGE,
					'edit_terms'   => GoWheels_Capabilities::MANAGE,
					'delete_terms' => GoWheels_Capabilities::MANAGE,
					'assign_terms' => GoWheels_Capabilities::MANAGE,
				),
			)
		);
	}

	/** Append vehicle details to the single vehicle page. */
	public static function single_content( $content ) {
		static $rendering = false;
		if ( $rendering ) {
			return $content;
		}

		if ( is_singular( self::TYPE ) && in_the_loop() && is_main_query() ) {
			if ( strpos( $content, 'gw-details' ) !== false || strpos( $content, '[gowheels_single' ) !== false || strpos( $content, '[gowheels_car' ) !== false ) {
				return $content;
			}

			$rendering = true;
			GoWheels_Assets::enqueue();
			$car = GoWheels_Vehicle_Service::data( get_the_ID() );
			if ( $car ) {
				$default_design = (string) GoWheels_Settings::get( 'default_single_design' );
				$design_map     = array(
					'1' => 'modern',  // Single Design 1 — Classic Detail
					'2' => 'classic', // Single Design 2 — Luxury Detail
					'3' => 'tabs',    // Single Design 3 — Split Layout
				);
				$default_tpl    = isset( $design_map[ $default_design ] ) ? $design_map[ $default_design ] : 'modern';
				$template       = apply_filters( 'gowheels_single_car_template', $default_tpl, $car );
				$tpl_file       = in_array( $template, array( 'modern', 'tabs', 'classic' ), true ) ? 'car-details-' . $template : 'car-details';
				$content       .= GoWheels_Util::template(
					$tpl_file,
					array(
						'car'      => $car,
						'book_url' => GoWheels_Shortcodes::book_url( $car['id'], array() ),
					)
				);
			}
			$rendering = false;
		}
		return $content;
	}
}
