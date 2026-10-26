<?php
defined( 'ABSPATH' ) || exit;

/**
 * Custom tables: bookings, payments, booking events.
 */
class GoWheels_Database {

	public static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'gw_' . $name;
	}

	public static function bookings() {
		return self::table( 'bookings' );
	}

	public static function payments() {
		return self::table( 'payments' );
	}

	public static function events() {
		return self::table( 'booking_events' );
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$c = $wpdb->get_charset_collate();

		$bookings = self::bookings();
		dbDelta(
			"CREATE TABLE {$bookings} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			booking_ref varchar(20) NOT NULL,
			vehicle_id bigint(20) unsigned NOT NULL,
			customer_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			customer_name varchar(190) NOT NULL,
			customer_email varchar(190) NOT NULL,
			customer_phone varchar(40) NOT NULL DEFAULT '',
			customer_notes text NULL,
			admin_notes text NULL,
			pickup_at_utc datetime NOT NULL,
			return_at_utc datetime NOT NULL,
			pickup_location_id bigint(20) unsigned NOT NULL DEFAULT 0,
			return_location_id bigint(20) unsigned NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'pending_review',
			payment_status varchar(20) NOT NULL DEFAULT 'unpaid',
			payment_preference varchar(20) NOT NULL DEFAULT 'cash',
			currency varchar(10) NOT NULL DEFAULT '',
			rental_subtotal decimal(12,2) NOT NULL DEFAULT 0.00,
			extras_total decimal(12,2) NOT NULL DEFAULT 0.00,
			discount_total decimal(12,2) NOT NULL DEFAULT 0.00,
			tax_total decimal(12,2) NOT NULL DEFAULT 0.00,
			grand_total decimal(12,2) NOT NULL DEFAULT 0.00,
			deposit_due decimal(12,2) NOT NULL DEFAULT 0.00,
			balance_due decimal(12,2) NOT NULL DEFAULT 0.00,
			price_snapshot longtext NULL,
			hold_expires_at_utc datetime NULL,
			created_at_utc datetime NOT NULL,
			updated_at_utc datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY booking_ref (booking_ref),
			KEY vehicle_status (vehicle_id,status),
			KEY pickup_return (pickup_at_utc,return_at_utc),
			KEY customer_email (customer_email),
			KEY status_hold (status,hold_expires_at_utc)
		) {$c};"
		);

		$payments = self::payments();
		dbDelta(
			"CREATE TABLE {$payments} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			booking_id bigint(20) unsigned NOT NULL,
			gateway varchar(30) NOT NULL DEFAULT 'cash',
			payment_type varchar(20) NOT NULL DEFAULT 'deposit',
			amount decimal(12,2) NOT NULL DEFAULT 0.00,
			currency varchar(10) NOT NULL DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'pending',
			gateway_transaction_id varchar(190) NOT NULL DEFAULT '',
			idempotency_key varchar(120) NULL,
			note varchar(255) NOT NULL DEFAULT '',
			created_at_utc datetime NOT NULL,
			paid_at_utc datetime NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY idempotency_key (idempotency_key),
			KEY booking_id (booking_id),
			KEY status (status)
		) {$c};"
		);

		$events = self::events();
		dbDelta(
			"CREATE TABLE {$events} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			booking_id bigint(20) unsigned NOT NULL,
			event_type varchar(40) NOT NULL,
			actor_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			message varchar(255) NOT NULL DEFAULT '',
			created_at_utc datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY booking_id (booking_id)
		) {$c};"
		);

		update_option( 'gowheels_db_version', GOWHEELS_DB_VERSION );
	}

	/** Runs on every load; only does work when the schema version changed. */
	public static function maybe_upgrade() {
		if ( get_option( 'gowheels_db_version' ) !== GOWHEELS_DB_VERSION ) {
			self::install();
			GoWheels_Capabilities::add();
		}
	}
}
