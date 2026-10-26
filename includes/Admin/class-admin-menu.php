<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Admin_Menu {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'wp_ajax_gowheels_save_active_design', array( __CLASS__, 'ajax_save_active_design' ) );
	}

	public static function menu() {
		$cap = GoWheels_Capabilities::MANAGE;
		add_menu_page( 'GoWheels', 'GoWheels', $cap, 'gowheels', array( __CLASS__, 'dashboard' ), 'dashicons-car', 26 );
		add_submenu_page( 'gowheels', __( 'Dashboard', 'gowheels' ), __( 'Dashboard', 'gowheels' ), $cap, 'gowheels', array( __CLASS__, 'dashboard' ) );
		add_submenu_page( 'gowheels', __( 'Design', 'gowheels' ), __( 'Design', 'gowheels' ), $cap, 'gowheels-design', array( __CLASS__, 'design_page' ) );
		add_submenu_page( 'gowheels', __( 'Bookings', 'gowheels' ), __( 'Bookings', 'gowheels' ), $cap, 'gowheels-bookings', array( 'GoWheels_Booking_List', 'render' ) );
		add_submenu_page( 'gowheels', __( 'Calendar', 'gowheels' ), __( 'Calendar', 'gowheels' ), $cap, 'gowheels-calendar', array( __CLASS__, 'calendar' ) );
		add_submenu_page( 'gowheels', __( 'Vehicles', 'gowheels' ), __( 'Vehicles', 'gowheels' ), 'edit_gowheels_vehicles', 'edit.php?post_type=' . GoWheels_Vehicle_CPT::TYPE );
		add_submenu_page( 'gowheels', __( 'Locations', 'gowheels' ), __( 'Locations', 'gowheels' ), $cap, 'edit-tags.php?taxonomy=' . GoWheels_Vehicle_CPT::TAX . '&post_type=' . GoWheels_Vehicle_CPT::TYPE );
		add_submenu_page( 'gowheels', __( 'Payments', 'gowheels' ), __( 'Payments', 'gowheels' ), $cap, 'gowheels-payments', array( __CLASS__, 'payments' ) );
		add_submenu_page( 'gowheels', __( 'Settings', 'gowheels' ), __( 'Settings', 'gowheels' ), $cap, 'gowheels-settings', array( 'GoWheels_Settings', 'render_page' ) );
	}

	public static function assets( $hook ) {
		$screen = get_current_screen();
		$is_gw  = false !== strpos( (string) $hook, 'gowheels' ) || ( $screen && GoWheels_Vehicle_CPT::TYPE === $screen->post_type );
		if ( $is_gw ) {
			wp_enqueue_style( 'gowheels-admin', GOWHEELS_URL . 'assets/css/admin.css', array(), GOWHEELS_VERSION );
			wp_enqueue_script( 'gowheels-admin', GOWHEELS_URL . 'assets/js/admin.js', array( 'jquery' ), GOWHEELS_VERSION, true );
			wp_localize_script(
				'gowheels-admin',
				'gowheelsDesignData',
				array(
					'ajaxurl'       => admin_url( 'admin-ajax.php' ),
					'nonce'         => wp_create_nonce( 'gw_design_nonce' ),
					'defaultCard'   => (string) GoWheels_Settings::get( 'default_card_design' ),
					'defaultSingle' => (string) GoWheels_Settings::get( 'default_single_design' ),
					'savedText'     => __( 'Selected ✓', 'gowheels' ),
					'chooseText'    => __( 'Choose design', 'gowheels' ),
				)
			);
		}
	}

	private static function guard() {
		if ( ! current_user_can( GoWheels_Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'gowheels' ) );
		}
	}

	/* ---------- Design Studio Page ---------- */

	public static function design_page() {
		self::guard();
		$vehicles = GoWheels_Vehicle_Service::all();
		require dirname( __FILE__ ) . '/views/design-studio.php';
	}

	public static function ajax_save_active_design() {
		check_ajax_referer( 'gw_design_nonce', 'nonce' );
		if ( ! current_user_can( GoWheels_Capabilities::MANAGE ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'gowheels' ) ) );
		}
		$type = isset( $_POST['design_type'] ) ? sanitize_key( wp_unslash( $_POST['design_type'] ) ) : '';
		$id   = isset( $_POST['design_id'] ) ? sanitize_key( wp_unslash( $_POST['design_id'] ) ) : '';

		if ( ! in_array( $id, array( '1', '2', '3' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid design identifier.', 'gowheels' ) ) );
		}

		if ( 'card' === $type ) {
			GoWheels_Settings::update( array( 'default_card_design' => $id ) );
			wp_send_json_success( array( 'message' => sprintf( __( 'Car Card Design %s set as default!', 'gowheels' ), $id ) ) );
		} elseif ( 'single' === $type ) {
			GoWheels_Settings::update( array( 'default_single_design' => $id ) );
			wp_send_json_success( array( 'message' => sprintf( __( 'Single Rental Design %s set as default!', 'gowheels' ), $id ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Unknown design type.', 'gowheels' ) ) );
	}

	/* ---------- Dashboard & Presets ---------- */

	public static function shortcodes_page() {
		self::dashboard( 'cards' );
	}

	public static function dashboard( $default_tab = 'overview' ) {
		global $wpdb;
		self::guard();
		GoWheels_Booking::expire_holds();
		$t        = GoWheels_Database::bookings();
		$counts   = $wpdb->get_results( "SELECT status, COUNT(*) c FROM {$t} GROUP BY status", OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL
		$p        = GoWheels_Database::payments();
		$revenue  = (float) $wpdb->get_var( "SELECT COALESCE(SUM(CASE WHEN payment_type='refund' AND status='refunded' THEN -amount WHEN status='paid' THEN amount ELSE 0 END),0) FROM {$p}" ); // phpcs:ignore WordPress.DB.PreparedSQL
		$pending  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} WHERE status = %s ORDER BY created_at_utc ASC LIMIT 10", 'pending_review' ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$upcoming = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} WHERE status = 'approved' AND pickup_at_utc BETWEEN %s AND %s ORDER BY pickup_at_utc ASC LIMIT 10", GoWheels_Util::now_utc(), gmdate( 'Y-m-d H:i:s', time() + 7 * DAY_IN_SECONDS ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $default_tab; // phpcs:ignore WordPress.Security.NonceVerification
		$valid_tabs = array( 'overview', 'cards', 'fleet-tabs', 'single-car', 'shortcodes' );
		if ( ! in_array( $active_tab, $valid_tabs, true ) ) {
			$active_tab = $default_tab;
		}

		$vehicles = GoWheels_Vehicle_Service::all();

		require dirname( __FILE__ ) . '/views/dashboard.php';
	}

	/* ---------- Calendar (vehicles × days) ---------- */

	public static function calendar() {
		global $wpdb;
		self::guard();
		$m = isset( $_GET['m'] ) ? sanitize_text_field( wp_unslash( $_GET['m'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! preg_match( '/^\d{4}-\d{2}$/', $m ) ) {
			$m = wp_date( 'Y-m' );
		}
		$first   = $m . '-01';
		$days    = (int) gmdate( 't', strtotime( $first ) );
		$start   = GoWheels_Util::to_utc( $first . ' 00:00' );
		$end     = GoWheels_Util::to_utc( gmdate( 'Y-m-d', strtotime( $first . ' +1 month' ) ) . ' 00:00' );
		$t       = GoWheels_Database::bookings();
		$rows    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} WHERE pickup_at_utc < %s AND return_at_utc > %s AND ( status IN ('approved','in_progress') OR (status = 'pending_review' AND hold_expires_at_utc > %s) )", $end, $start, GoWheels_Util::now_utc() ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		$grid = array();
		foreach ( $rows as $b ) {
			$d0 = max( strtotime( wp_date( 'Y-m-d', GoWheels_Util::ts( $b->pickup_at_utc ) ) ), strtotime( $first ) );
			$d1 = min( strtotime( wp_date( 'Y-m-d', GoWheels_Util::ts( $b->return_at_utc ) ) ), strtotime( $first . ' +' . ( $days - 1 ) . ' days' ) );
			for ( $d = $d0; $d <= $d1; $d += DAY_IN_SECONDS ) {
				$grid[ $b->vehicle_id ][ (int) gmdate( 'j', $d ) ] = array( $b->status, $b->booking_ref, (int) $b->id );
			}
		}

		$prev = gmdate( 'Y-m', strtotime( $first . ' -1 month' ) );
		$next = gmdate( 'Y-m', strtotime( $first . ' +1 month' ) );
		$base = admin_url( 'admin.php?page=gowheels-calendar' );
		echo '<div class="wrap gw-admin"><h1>' . esc_html__( 'Calendar', 'gowheels' ) . ' — ' . esc_html( wp_date( 'F Y', strtotime( $first ) ) ) . '</h1>';
		echo '<p><a class="button" href="' . esc_url( add_query_arg( 'm', $prev, $base ) ) . '">&larr;</a> <a class="button" href="' . esc_url( add_query_arg( 'm', $next, $base ) ) . '">&rarr;</a> ';
		echo '<span class="gw-badge gw-badge-approved">' . esc_html__( 'Approved', 'gowheels' ) . '</span> <span class="gw-badge gw-badge-in_progress">' . esc_html__( 'In progress', 'gowheels' ) . '</span> <span class="gw-badge gw-badge-pending_review">' . esc_html__( 'Held (pending)', 'gowheels' ) . '</span></p>';
		echo '<div style="overflow-x:auto"><table class="gw-cal"><thead><tr><th>' . esc_html__( 'Vehicle', 'gowheels' ) . '</th>';
		for ( $d = 1; $d <= $days; $d++ ) {
			echo '<th>' . (int) $d . '</th>';
		}
		echo '</tr></thead><tbody>';
		foreach ( GoWheels_Vehicle_Service::ids() as $vid ) {
			echo '<tr><th class="gw-cal-v">' . esc_html( get_the_title( $vid ) ) . '</th>';
			for ( $d = 1; $d <= $days; $d++ ) {
				if ( isset( $grid[ $vid ][ $d ] ) ) {
					list( $status, $ref, $bid ) = $grid[ $vid ][ $d ];
					printf( '<td class="gw-cal-%s"><a href="%s" title="%s"></a></td>', esc_attr( $status ), esc_url( admin_url( 'admin.php?page=gowheels-bookings&view=' . $bid ) ), esc_attr( $ref ) );
				} else {
					echo '<td></td>';
				}
			}
			echo '</tr>';
		}
		echo '</tbody></table></div></div>';
	}

	/* ---------- Payments ---------- */

	public static function payments() {
		global $wpdb;
		self::guard();
		$p = GoWheels_Database::payments();
		$b = GoWheels_Database::bookings();

		$out = $wpdb->get_results( "SELECT * FROM {$b} WHERE status IN ('approved','in_progress') AND balance_due > 0 ORDER BY pickup_at_utc ASC LIMIT 50" ); // phpcs:ignore WordPress.DB.PreparedSQL
		$rec = $wpdb->get_results( "SELECT p.*, b.booking_ref FROM {$p} p JOIN {$b} b ON b.id = p.booking_id ORDER BY p.id DESC LIMIT 100" ); // phpcs:ignore WordPress.DB.PreparedSQL

		echo '<div class="wrap gw-admin"><h1>' . esc_html__( 'Payments', 'gowheels' ) . '</h1><h2>' . esc_html__( 'Outstanding balances (approved bookings)', 'gowheels' ) . '</h2><table class="widefat striped"><thead><tr><th>Ref</th><th>' . esc_html__( 'Customer', 'gowheels' ) . '</th><th>' . esc_html__( 'Pickup', 'gowheels' ) . '</th><th>' . esc_html__( 'Total', 'gowheels' ) . '</th><th>' . esc_html__( 'Balance', 'gowheels' ) . '</th></tr></thead><tbody>';
		if ( ! $out ) {
			echo '<tr><td colspan="5">' . esc_html__( 'Nothing outstanding.', 'gowheels' ) . '</td></tr>';
		}
		foreach ( $out as $r ) {
			printf( '<tr><td><a href="%s">%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', esc_url( admin_url( 'admin.php?page=gowheels-bookings&view=' . (int) $r->id ) ), esc_html( $r->booking_ref ), esc_html( $r->customer_name ), esc_html( GoWheels_Util::to_local( $r->pickup_at_utc ) ), esc_html( GoWheels_Util::money( $r->grand_total ) ), esc_html( GoWheels_Util::money( $r->balance_due ) ) );
		}
		echo '</tbody></table><h2>' . esc_html__( 'Recent transactions', 'gowheels' ) . '</h2><table class="widefat striped"><thead><tr><th>#</th><th>Ref</th><th>' . esc_html__( 'Type', 'gowheels' ) . '</th><th>' . esc_html__( 'Method', 'gowheels' ) . '</th><th>' . esc_html__( 'Amount', 'gowheels' ) . '</th><th>' . esc_html__( 'Status', 'gowheels' ) . '</th><th>' . esc_html__( 'Date', 'gowheels' ) . '</th></tr></thead><tbody>';
		foreach ( $rec as $r ) {
			printf( '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', (int) $r->id, esc_html( $r->booking_ref ), esc_html( $r->payment_type ), esc_html( $r->gateway ), esc_html( GoWheels_Util::money( $r->amount ) ), esc_html( $r->status ), esc_html( GoWheels_Util::to_local( $r->paid_at_utc ? $r->paid_at_utc : $r->created_at_utc ) ) );
		}
		echo '</tbody></table></div>';
	}
}
