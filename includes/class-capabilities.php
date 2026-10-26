<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Capabilities {

	const MANAGE = 'gowheels_manage_bookings';
	const ROLE   = 'gowheels_manager';

	public static function vehicle_caps() {
		$p = 'gowheels_vehicles';
		return array(
			"edit_{$p}",
			"edit_others_{$p}",
			"publish_{$p}",
			"read_private_{$p}",
			"delete_{$p}",
			"delete_private_{$p}",
			"delete_published_{$p}",
			"delete_others_{$p}",
			"edit_private_{$p}",
			"edit_published_{$p}",
		);
	}

	public static function add() {
		$caps = array_merge( array( self::MANAGE ), self::vehicle_caps() );

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			foreach ( $caps as $cap ) {
				$admin->add_cap( $cap );
			}
		}

		if ( ! get_role( self::ROLE ) ) {
			add_role( self::ROLE, __( 'GoWheels Manager', 'gowheels' ), array( 'read' => true, 'upload_files' => true ) );
		}
		$mgr = get_role( self::ROLE );
		if ( $mgr ) {
			foreach ( $caps as $cap ) {
				$mgr->add_cap( $cap );
			}
		}
	}

	public static function remove() {
		$caps  = array_merge( array( self::MANAGE ), self::vehicle_caps() );
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			foreach ( $caps as $cap ) {
				$admin->remove_cap( $cap );
			}
		}
		remove_role( self::ROLE );
	}
}
