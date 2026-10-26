<?php
defined( 'ABSPATH' ) || exit;

/**
 * Overlap logic. Two bookings overlap when:
 *   existing_pickup < new_return  AND  existing_return > new_pickup
 * (buffer minutes are added to both sides of existing bookings).
 */
class GoWheels_Availability {

	/** Is there an active booking / unexpired hold overlapping this window? */
	public static function has_conflict( $vehicle_id, $pickup_utc, $return_utc, $exclude_id = 0 ) {
		global $wpdb;
		$t      = GoWheels_Database::bookings();
		$buffer = (int) GoWheels_Settings::get( 'buffer_minutes' );
		$now    = GoWheels_Util::now_utc();

		$sql = $wpdb->prepare(
			"SELECT id FROM {$t}
			 WHERE vehicle_id = %d
			   AND id <> %d
			   AND ( status IN ('approved','in_progress') OR ( status = 'pending_review' AND hold_expires_at_utc > %s ) )
			   AND DATE_SUB(pickup_at_utc, INTERVAL %d MINUTE) < %s
			   AND DATE_ADD(return_at_utc, INTERVAL %d MINUTE) > %s
			 LIMIT 1",
			$vehicle_id,
			$exclude_id,
			$now,
			$buffer,
			$return_utc,
			$buffer,
			$pickup_utc
		);
		return (bool) $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function in_blocked_range( $vehicle_id, $pickup_utc, $return_utc ) {
		foreach ( GoWheels_Vehicle_Service::blocked_ranges( $vehicle_id ) as $range ) {
			if ( $range[0] < $return_utc && $range[1] > $pickup_utc ) {
				return true;
			}
		}
		return false;
	}

	public static function is_available( $vehicle_id, $pickup_utc, $return_utc, $exclude_id = 0 ) {
		if ( self::in_blocked_range( $vehicle_id, $pickup_utc, $return_utc ) ) {
			return false;
		}
		return ! self::has_conflict( $vehicle_id, $pickup_utc, $return_utc, $exclude_id );
	}

	/** Published vehicle IDs available for the window (optionally filtered by pickup location). */
	public static function available_vehicle_ids( $pickup_utc, $return_utc, $pickup_location = 0 ) {
		$out = array();
		foreach ( GoWheels_Vehicle_Service::ids() as $id ) {
			$home = (int) get_post_meta( $id, '_gw_location_id', true );
			if ( $pickup_location && $home && $home !== (int) $pickup_location ) {
				continue;
			}
			if ( self::is_available( $id, $pickup_utc, $return_utc ) ) {
				$out[] = $id;
			}
		}
		return $out;
	}
}
