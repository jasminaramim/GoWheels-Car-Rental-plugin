<?php
/**
 * Uninstall: data is removed ONLY if "Delete all data on uninstall" is enabled in settings.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$gw_settings = get_option( 'gowheels_settings', array() );
if ( empty( $gw_settings['delete_on_uninstall'] ) ) {
	return;
}

global $wpdb;

foreach ( array( 'gw_bookings', 'gw_payments', 'gw_booking_events' ) as $gw_table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$gw_table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}

$gw_vehicles = get_posts(
	array(
		'post_type'   => 'gowheels_vehicle',
		'post_status' => 'any',
		'numberposts' => -1,
		'fields'      => 'ids',
	)
);
foreach ( $gw_vehicles as $gw_id ) {
	wp_delete_post( $gw_id, true );
}

register_taxonomy( 'gowheels_location', 'gowheels_vehicle' );
$gw_terms = get_terms( array( 'taxonomy' => 'gowheels_location', 'hide_empty' => false ) );
if ( ! is_wp_error( $gw_terms ) ) {
	foreach ( $gw_terms as $gw_term ) {
		wp_delete_term( $gw_term->term_id, 'gowheels_location' );
	}
}

delete_option( 'gowheels_settings' );
delete_option( 'gowheels_db_version' );
wp_clear_scheduled_hook( 'gowheels_expire_holds' );

$gw_caps = array(
	'gowheels_manage_bookings',
	'edit_gowheels_vehicles', 'edit_others_gowheels_vehicles', 'publish_gowheels_vehicles',
	'read_private_gowheels_vehicles', 'delete_gowheels_vehicles', 'delete_private_gowheels_vehicles',
	'delete_published_gowheels_vehicles', 'delete_others_gowheels_vehicles',
	'edit_private_gowheels_vehicles', 'edit_published_gowheels_vehicles',
);
$gw_admin = get_role( 'administrator' );
if ( $gw_admin ) {
	foreach ( $gw_caps as $gw_cap ) {
		$gw_admin->remove_cap( $gw_cap );
	}
}
remove_role( 'gowheels_manager' );
