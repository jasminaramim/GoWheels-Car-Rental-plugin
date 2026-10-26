<?php
defined( 'ABSPATH' ) || exit;

/**
 * Deactivation never deletes bookings, payments or vehicles.
 */
class GoWheels_Deactivator {
	public static function deactivate() {
		wp_clear_scheduled_hook( 'gowheels_expire_holds' );
		flush_rewrite_rules();
	}
}
