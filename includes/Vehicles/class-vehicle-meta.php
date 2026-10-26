<?php
defined( 'ABSPATH' ) || exit;

/**
 * Vehicle meta box (pricing, specs, gallery, availability blocks).
 */
class GoWheels_Vehicle_Meta {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_box' ) );
		add_action( 'save_post_' . GoWheels_Vehicle_CPT::TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function transmissions() {
		return array(
			'automatic' => __( 'Automatic', 'gowheels' ),
			'manual'    => __( 'Manual', 'gowheels' ),
		);
	}

	public static function fuels() {
		return array(
			'petrol'   => __( 'Petrol', 'gowheels' ),
			'octane'   => __( 'Octane', 'gowheels' ),
			'diesel'   => __( 'Diesel', 'gowheels' ),
			'cng'      => __( 'CNG', 'gowheels' ),
			'hybrid'   => __( 'Hybrid', 'gowheels' ),
			'electric' => __( 'Electric', 'gowheels' ),
		);
	}

	public static function add_box() {
		add_meta_box( 'gowheels_vehicle', __( 'Vehicle details & pricing', 'gowheels' ), array( __CLASS__, 'render' ), GoWheels_Vehicle_CPT::TYPE, 'normal', 'high' );
	}

	public static function assets( $hook ) {
		$screen = get_current_screen();
		if ( $screen && GoWheels_Vehicle_CPT::TYPE === $screen->post_type && in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'gowheels-admin', GOWHEELS_URL . 'assets/js/admin.js', array( 'jquery' ), GOWHEELS_VERSION, true );
		}
	}

	private static function meta( $post_id, $key ) {
		return get_post_meta( $post_id, '_gw_' . $key, true );
	}

	public static function render( $post ) {
		wp_nonce_field( 'gowheels_save_vehicle_' . $post->ID, 'gowheels_vehicle_nonce' );
		$m = function ( $k ) use ( $post ) {
			return self::meta( $post->ID, $k );
		};
		$locations = GoWheels_Util::locations();
		?>
		<table class="form-table" role="presentation">
			<tr><th><label for="gw_price_daily"><?php esc_html_e( 'Daily price', 'gowheels' ); ?></label></th>
				<td><input type="number" step="0.01" min="0" id="gw_price_daily" name="gw_price_daily" value="<?php echo esc_attr( $m( 'price_daily' ) ); ?>" required></td></tr>
			<tr><th><label for="gw_price_hourly"><?php esc_html_e( 'Hourly price (optional)', 'gowheels' ); ?></label></th>
				<td><input type="number" step="0.01" min="0" id="gw_price_hourly" name="gw_price_hourly" value="<?php echo esc_attr( $m( 'price_hourly' ) ); ?>">
				<p class="description"><?php esc_html_e( 'Used only for rentals of 24 hours or less (capped at the daily price).', 'gowheels' ); ?></p></td></tr>
			<tr><th><label for="gw_min_rental"><?php esc_html_e( 'Minimum rental (days)', 'gowheels' ); ?></label></th>
				<td><input type="number" min="1" id="gw_min_rental" name="gw_min_rental" value="<?php echo esc_attr( $m( 'min_rental' ) ? $m( 'min_rental' ) : 1 ); ?>"></td></tr>
			<tr><th><label for="gw_transmission"><?php esc_html_e( 'Transmission', 'gowheels' ); ?></label></th>
				<td><select id="gw_transmission" name="gw_transmission"><?php foreach ( self::transmissions() as $k => $l ) { printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $m( 'transmission' ), $k, false ), esc_html( $l ) ); } ?></select></td></tr>
			<tr><th><label for="gw_fuel_type"><?php esc_html_e( 'Fuel type', 'gowheels' ); ?></label></th>
				<td><select id="gw_fuel_type" name="gw_fuel_type"><?php foreach ( self::fuels() as $k => $l ) { printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $m( 'fuel_type' ), $k, false ), esc_html( $l ) ); } ?></select></td></tr>
			<tr><th><label for="gw_seats"><?php esc_html_e( 'Seats', 'gowheels' ); ?></label></th>
				<td><input type="number" min="1" max="60" id="gw_seats" name="gw_seats" value="<?php echo esc_attr( $m( 'seats' ) ); ?>"></td></tr>
			<tr><th><label for="gw_luggage"><?php esc_html_e( 'Luggage capacity', 'gowheels' ); ?></label></th>
				<td><input type="number" min="0" max="50" id="gw_luggage" name="gw_luggage" value="<?php echo esc_attr( $m( 'luggage' ) ); ?>"></td></tr>
			<tr><th><label for="gw_location_id"><?php esc_html_e( 'Home pickup location', 'gowheels' ); ?></label></th>
				<td><select id="gw_location_id" name="gw_location_id">
					<option value="0"><?php esc_html_e( 'Any location', 'gowheels' ); ?></option>
					<?php foreach ( $locations as $id => $name ) { printf( '<option value="%d" %s>%s</option>', (int) $id, selected( (int) $m( 'location_id' ), (int) $id, false ), esc_html( $name ) ); } ?>
				</select>
				<p class="description"><?php esc_html_e( 'Add locations under GoWheels → Locations.', 'gowheels' ); ?></p></td></tr>
			<tr><th><label for="gw_features"><?php esc_html_e( 'Included features', 'gowheels' ); ?></label></th>
				<td><textarea id="gw_features" name="gw_features" rows="4" class="large-text"><?php echo esc_textarea( $m( 'features' ) ); ?></textarea>
				<p class="description"><?php esc_html_e( 'One feature per line (A/C, Bluetooth, GPS…).', 'gowheels' ); ?></p></td></tr>
			<tr><th><label for="gw_blocked_ranges"><?php esc_html_e( 'Maintenance / blocked dates', 'gowheels' ); ?></label></th>
				<td><textarea id="gw_blocked_ranges" name="gw_blocked_ranges" rows="3" class="large-text" placeholder="2026-12-01 to 2026-12-05"><?php echo esc_textarea( $m( 'blocked_ranges' ) ); ?></textarea>
				<p class="description"><?php esc_html_e( 'One range per line: YYYY-MM-DD to YYYY-MM-DD (inclusive). The car cannot be booked on these days.', 'gowheels' ); ?></p></td></tr>
			<tr><th><?php esc_html_e( 'Image gallery', 'gowheels' ); ?></th>
				<td>
					<input type="hidden" id="gw_gallery" name="gw_gallery" value="<?php echo esc_attr( $m( 'gallery' ) ); ?>">
					<div id="gw-gallery-preview" class="gw-gallery-preview">
						<?php
						foreach ( array_filter( array_map( 'absint', explode( ',', (string) $m( 'gallery' ) ) ) ) as $att ) {
							echo wp_get_attachment_image( $att, 'thumbnail' );
						}
						?>
					</div>
					<button type="button" class="button" id="gw-gallery-btn"><?php esc_html_e( 'Select images', 'gowheels' ); ?></button>
					<p class="description"><?php esc_html_e( 'The featured image is the main photo; the gallery is shown on the vehicle page.', 'gowheels' ); ?></p>
				</td></tr>
		</table>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['gowheels_vehicle_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gowheels_vehicle_nonce'] ) ), 'gowheels_save_vehicle_' . $post_id ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$num = function ( $key ) {
			return isset( $_POST[ $key ] ) ? max( 0, round( (float) wp_unslash( $_POST[ $key ] ), 2 ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		};
		$int = function ( $key ) {
			return isset( $_POST[ $key ] ) ? absint( wp_unslash( $_POST[ $key ] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		};

		update_post_meta( $post_id, '_gw_price_daily', $num( 'gw_price_daily' ) );
		update_post_meta( $post_id, '_gw_price_hourly', $num( 'gw_price_hourly' ) );
		update_post_meta( $post_id, '_gw_min_rental', max( 1, $int( 'gw_min_rental' ) ) );
		update_post_meta( $post_id, '_gw_seats', $int( 'gw_seats' ) );
		update_post_meta( $post_id, '_gw_luggage', $int( 'gw_luggage' ) );

		$trans = isset( $_POST['gw_transmission'] ) ? sanitize_key( wp_unslash( $_POST['gw_transmission'] ) ) : 'automatic';
		update_post_meta( $post_id, '_gw_transmission', isset( self::transmissions()[ $trans ] ) ? $trans : 'automatic' );

		$fuel = isset( $_POST['gw_fuel_type'] ) ? sanitize_key( wp_unslash( $_POST['gw_fuel_type'] ) ) : 'petrol';
		update_post_meta( $post_id, '_gw_fuel_type', isset( self::fuels()[ $fuel ] ) ? $fuel : 'petrol' );

		$loc = $int( 'gw_location_id' );
		update_post_meta( $post_id, '_gw_location_id', ( 0 === $loc || isset( GoWheels_Util::locations()[ $loc ] ) ) ? $loc : 0 );

		update_post_meta( $post_id, '_gw_features', isset( $_POST['gw_features'] ) ? sanitize_textarea_field( wp_unslash( $_POST['gw_features'] ) ) : '' );

		// Blocked ranges: keep only well-formed lines.
		$lines = array();
		if ( isset( $_POST['gw_blocked_ranges'] ) ) {
			foreach ( preg_split( '/\r\n|\r|\n/', sanitize_textarea_field( wp_unslash( $_POST['gw_blocked_ranges'] ) ) ) as $line ) {
				if ( preg_match( '/^(\d{4}-\d{2}-\d{2})\s+to\s+(\d{4}-\d{2}-\d{2})$/', trim( $line ), $mm ) && $mm[1] <= $mm[2] ) {
					$lines[] = $mm[1] . ' to ' . $mm[2];
				}
			}
		}
		update_post_meta( $post_id, '_gw_blocked_ranges', implode( "\n", $lines ) );

		$ids = isset( $_POST['gw_gallery'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['gw_gallery'] ) ) ) ) ) : array();
		update_post_meta( $post_id, '_gw_gallery', implode( ',', $ids ) );
	}
}
