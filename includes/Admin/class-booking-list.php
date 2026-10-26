<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin: bookings list, booking detail, workflow actions, CSV export.
 */
class GoWheels_Booking_List {

	public static function init() {
		add_action( 'admin_post_gowheels_booking_action', array( __CLASS__, 'handle_action' ) );
		add_action( 'admin_post_gowheels_record_payment', array( __CLASS__, 'handle_payment' ) );
		add_action( 'admin_post_gowheels_save_notes', array( __CLASS__, 'handle_notes' ) );
		add_action( 'admin_post_gowheels_export', array( __CLASS__, 'handle_export' ) );
	}

	/* ---------- Notices (stored per user; never reflected from the URL) ---------- */

	private static function flash( $type, $msg ) {
		set_transient( 'gw_notice_' . get_current_user_id(), array( $type, $msg ), 60 );
	}

	public static function show_notice() {
		$key = 'gw_notice_' . get_current_user_id();
		$n   = get_transient( $key );
		if ( $n ) {
			delete_transient( $key );
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', 'error' === $n[0] ? 'error' : 'success', esc_html( $n[1] ) );
		}
	}

	private static function back( $id = 0 ) {
		$args = array( 'page' => 'gowheels-bookings' );
		if ( $id ) {
			$args['view'] = (int) $id;
		}
		wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
		exit;
	}

	private static function guard( $nonce_action ) {
		if ( ! current_user_can( GoWheels_Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'gowheels' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( $nonce_action );
	}

	/* ---------- Handlers ---------- */

	public static function handle_action() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		self::guard( 'gowheels_booking_' . $id );
		$do   = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
		$note = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';
		$map  = array(
			'approve'  => GoWheels_Booking_Status::APPROVED,
			'reject'   => GoWheels_Booking_Status::REJECTED,
			'cancel'   => GoWheels_Booking_Status::CANCELLED,
			'start'    => GoWheels_Booking_Status::IN_PROGRESS,
			'complete' => GoWheels_Booking_Status::COMPLETED,
		);

		if ( 'extend' === $do ) {
			$r = GoWheels_Booking::extend_hold( $id, max( 1, (int) GoWheels_Settings::get( 'hold_minutes' ) ), get_current_user_id() );
		} elseif ( isset( $map[ $do ] ) ) {
			$r = GoWheels_Booking::transition( $id, $map[ $do ], get_current_user_id(), $note );
		} else {
			$r = new WP_Error( 'gowheels_bad_action', __( 'Unknown action.', 'gowheels' ) );
		}
		is_wp_error( $r ) ? self::flash( 'error', $r->get_error_message() ) : self::flash( 'success', __( 'Booking updated.', 'gowheels' ) );
		self::back( $id );
	}

	public static function handle_payment() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		self::guard( 'gowheels_payment_' . $id );
		$amount = isset( $_POST['amount'] ) ? (float) wp_unslash( $_POST['amount'] ) : 0;
		$kind   = isset( $_POST['kind'] ) ? sanitize_key( wp_unslash( $_POST['kind'] ) ) : 'payment';
		$type   = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'deposit';
		$method = isset( $_POST['method'] ) ? sanitize_key( wp_unslash( $_POST['method'] ) ) : 'cash';
		$note   = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';

		$r = 'refund' === $kind
			? GoWheels_Payments::refund( $id, $amount, $note, get_current_user_id() )
			: GoWheels_Payments::add_manual( $id, $amount, $type, $method, $note, get_current_user_id() );
		is_wp_error( $r ) ? self::flash( 'error', $r->get_error_message() ) : self::flash( 'success', __( 'Payment recorded.', 'gowheels' ) );
		self::back( $id );
	}

	public static function handle_notes() {
		global $wpdb;
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		self::guard( 'gowheels_notes_' . $id );
		$notes = isset( $_POST['admin_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_notes'] ) ) : '';
		$wpdb->update( GoWheels_Database::bookings(), array( 'admin_notes' => $notes ), array( 'id' => $id ) );
		self::flash( 'success', __( 'Notes saved.', 'gowheels' ) );
		self::back( $id );
	}

	public static function handle_export() {
		global $wpdb;
		self::guard( 'gowheels_export' );
		$t    = GoWheels_Database::bookings();
		$rows = $wpdb->get_results( "SELECT * FROM {$t} ORDER BY id DESC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=gowheels-bookings-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'Ref', 'Status', 'Payment', 'Vehicle', 'Customer', 'Email', 'Phone', 'Pickup (UTC)', 'Return (UTC)', 'Total', 'Deposit', 'Balance', 'Created (UTC)' ) );
		$safe = function ( $v ) { // Neutralise spreadsheet formula injection.
			$v = (string) $v;
			return ( '' !== $v && in_array( $v[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) ? "'" . $v : $v;
		};
		foreach ( $rows as $r ) {
			fputcsv(
				$out,
				array_map(
					$safe,
					array( $r['booking_ref'], $r['status'], $r['payment_status'], get_the_title( $r['vehicle_id'] ), $r['customer_name'], $r['customer_email'], $r['customer_phone'], $r['pickup_at_utc'], $r['return_at_utc'], $r['grand_total'], $r['deposit_due'], $r['balance_due'], $r['created_at_utc'] )
				)
			);
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}

	/* ---------- Pages ---------- */

	public static function render() {
		if ( ! current_user_can( GoWheels_Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'gowheels' ) );
		}
		GoWheels_Booking::expire_holds();
		echo '<div class="wrap gw-admin">';
		self::show_notice();
		if ( isset( $_GET['view'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::render_detail( absint( $_GET['view'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		} else {
			self::render_list();
		}
		echo '</div>';
	}

	private static function render_list() {
		global $wpdb;
		$t      = GoWheels_Database::bookings();
		$status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$paged  = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification
		$per    = 20;

		$where = array( '1=1' );
		$args  = array();
		if ( isset( GoWheels_Booking_Status::labels()[ $status ] ) ) {
			$where[] = 'status = %s';
			$args[]  = $status;
		}
		if ( '' !== $search ) {
			$like    = '%' . $wpdb->esc_like( $search ) . '%';
			$where[] = '(booking_ref LIKE %s OR customer_name LIKE %s OR customer_email LIKE %s OR customer_phone LIKE %s)';
			array_push( $args, $like, $like, $like, $like );
		}
		$w     = implode( ' AND ', $where );
		$count = (int) ( $args ? $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t} WHERE {$w}", $args ) ) : $wpdb->get_var( "SELECT COUNT(*) FROM {$t}" ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$q     = $args;
		$q[]   = $per;
		$q[]   = ( $paged - 1 ) * $per;
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} WHERE {$w} ORDER BY id DESC LIMIT %d OFFSET %d", $q ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		$base = admin_url( 'admin.php?page=gowheels-bookings' );
		echo '<h1 class="wp-heading-inline">' . esc_html__( 'Bookings', 'gowheels' ) . '</h1> ';
		echo '<a class="page-title-action" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=gowheels_export' ), 'gowheels_export' ) ) . '">' . esc_html__( 'Export CSV', 'gowheels' ) . '</a><hr class="wp-header-end">';

		echo '<ul class="subsubsub"><li><a href="' . esc_url( $base ) . '"' . ( '' === $status ? ' class="current"' : '' ) . '>' . esc_html__( 'All', 'gowheels' ) . '</a></li>';
		foreach ( GoWheels_Booking_Status::labels() as $k => $l ) {
			echo ' | <li><a href="' . esc_url( add_query_arg( 'status', $k, $base ) ) . '"' . ( $status === $k ? ' class="current"' : '' ) . '>' . esc_html( $l ) . '</a></li>';
		}
		echo '</ul>';
		echo '<form method="get" class="search-box"><input type="hidden" name="page" value="gowheels-bookings"><input type="search" name="s" value="' . esc_attr( $search ) . '" placeholder="' . esc_attr__( 'Ref, name, email, phone', 'gowheels' ) . '"> <button class="button">' . esc_html__( 'Search', 'gowheels' ) . '</button></form>';

		echo '<table class="widefat striped"><thead><tr>';
		foreach ( array( 'Ref', 'Customer', 'Vehicle', 'Pickup', 'Return', 'Total', 'Status', 'Payment' ) as $h ) {
			echo '<th>' . esc_html( $h ) . '</th>';
		}
		echo '</tr></thead><tbody>';
		if ( ! $rows ) {
			echo '<tr><td colspan="8">' . esc_html__( 'No bookings found.', 'gowheels' ) . '</td></tr>';
		}
		foreach ( $rows as $b ) {
			printf(
				'<tr><td><a href="%s"><strong>%s</strong></a></td><td>%s<br><small>%s</small></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
				esc_url( add_query_arg( 'view', (int) $b->id, $base ) ),
				esc_html( $b->booking_ref ),
				esc_html( $b->customer_name ),
				esc_html( $b->customer_email ),
				esc_html( get_the_title( $b->vehicle_id ) ),
				esc_html( GoWheels_Util::to_local( $b->pickup_at_utc ) ),
				esc_html( GoWheels_Util::to_local( $b->return_at_utc ) ),
				esc_html( GoWheels_Util::money( $b->grand_total ) ),
				GoWheels_Booking_Status::badge( $b->status ), // phpcs:ignore WordPress.Security.EscapeOutput
				GoWheels_Booking_Status::badge( $b->payment_status, true ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
		}
		echo '</tbody></table>';
		$pages = (int) ceil( $count / $per );
		if ( $pages > 1 ) {
			echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%' ), 'format' => '', 'current' => $paged, 'total' => $pages ) ) ) . '</div></div>';
		}
	}

	private static function render_detail( $id ) {
		$b = GoWheels_Booking::get( $id );
		if ( ! $b ) {
			echo '<h1>' . esc_html__( 'Booking not found', 'gowheels' ) . '</h1>';
			return;
		}
		global $wpdb;
		$view     = GoWheels_Booking::public_view( $b );
		$payments = GoWheels_Payments::for_booking( $id );
		$events   = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . GoWheels_Database::events() . ' WHERE booking_id = %d ORDER BY id DESC', $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$st       = $b->status;
		$action   = esc_url( admin_url( 'admin-post.php' ) );

		echo '<h1>' . esc_html( $b->booking_ref ) . ' ' . GoWheels_Booking_Status::badge( $st ) . ' ' . GoWheels_Booking_Status::badge( $b->payment_status, true ) . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=gowheels-bookings' ) ) . '">&larr; ' . esc_html__( 'All bookings', 'gowheels' ) . '</a></p>';
		echo '<div class="gw-cols">';

		// Left column.
		echo '<div class="gw-col"><div class="postbox"><h2 class="hndle">' . esc_html__( 'Details', 'gowheels' ) . '</h2><div class="inside"><table class="gw-kv">';
		$kv = array(
			__( 'Customer', 'gowheels' )        => $b->customer_name,
			__( 'Email', 'gowheels' )           => $b->customer_email,
			__( 'Phone', 'gowheels' )           => $b->customer_phone,
			__( 'Vehicle', 'gowheels' )         => get_the_title( $b->vehicle_id ),
			__( 'Pickup', 'gowheels' )          => GoWheels_Util::to_local( $b->pickup_at_utc ) . ( $view['pickup_location'] ? ' — ' . $view['pickup_location'] : '' ),
			__( 'Return', 'gowheels' )          => GoWheels_Util::to_local( $b->return_at_utc ) . ( $view['return_location'] ? ' — ' . $view['return_location'] : '' ),
			__( 'Payment preference', 'gowheels' ) => $b->payment_preference,
			__( 'Customer notes', 'gowheels' )  => $b->customer_notes,
			__( 'Hold expires', 'gowheels' )    => $view['hold_expires'] ? $view['hold_expires'] : '—',
			__( 'Created', 'gowheels' )         => GoWheels_Util::to_local( $b->created_at_utc ),
		);
		foreach ( $kv as $k => $v ) {
			echo '<tr><th>' . esc_html( $k ) . '</th><td>' . nl2br( esc_html( $v ) ) . '</td></tr>';
		}
		echo '</table></div></div>';

		echo '<div class="postbox"><h2 class="hndle">' . esc_html__( 'Price', 'gowheels' ) . '</h2><div class="inside"><table class="gw-kv">';
		foreach ( $view['lines'] as $l ) {
			echo '<tr><th>' . esc_html( $l['label'] ) . '</th><td>' . esc_html( GoWheels_Util::money( $l['amount'] ) ) . '</td></tr>';
		}
		echo '<tr><th><strong>' . esc_html__( 'Grand total', 'gowheels' ) . '</strong></th><td><strong>' . esc_html( GoWheels_Util::money( $b->grand_total ) ) . '</strong></td></tr>';
		echo '<tr><th>' . esc_html__( 'Deposit required', 'gowheels' ) . '</th><td>' . esc_html( GoWheels_Util::money( $b->deposit_due ) ) . '</td></tr>';
		echo '<tr><th>' . esc_html__( 'Paid (net)', 'gowheels' ) . '</th><td>' . esc_html( GoWheels_Util::money( $view['paid'] ) ) . '</td></tr>';
		echo '<tr><th>' . esc_html__( 'Balance due', 'gowheels' ) . '</th><td>' . esc_html( GoWheels_Util::money( $view['balance_due'] ) ) . '</td></tr>';
		echo '</table></div></div>';

		echo '<div class="postbox"><h2 class="hndle">' . esc_html__( 'Payments', 'gowheels' ) . '</h2><div class="inside">';
		if ( $payments ) {
			echo '<table class="widefat striped"><thead><tr><th>#</th><th>' . esc_html__( 'Type', 'gowheels' ) . '</th><th>' . esc_html__( 'Method', 'gowheels' ) . '</th><th>' . esc_html__( 'Amount', 'gowheels' ) . '</th><th>' . esc_html__( 'Status', 'gowheels' ) . '</th><th>' . esc_html__( 'Date', 'gowheels' ) . '</th></tr></thead><tbody>';
			foreach ( $payments as $p ) {
				printf( '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', (int) $p->id, esc_html( $p->payment_type ), esc_html( $p->gateway ), esc_html( GoWheels_Util::money( $p->amount ) ), esc_html( $p->status ), esc_html( GoWheels_Util::to_local( $p->paid_at_utc ? $p->paid_at_utc : $p->created_at_utc ) ) );
			}
			echo '</tbody></table>';
		} else {
			echo '<p>' . esc_html__( 'No payments yet.', 'gowheels' ) . '</p>';
		}
		echo '<h3>' . esc_html__( 'Record payment / refund', 'gowheels' ) . '</h3>';
		echo '<form method="post" action="' . $action . '">'; // phpcs:ignore WordPress.Security.EscapeOutput
		wp_nonce_field( 'gowheels_payment_' . $id );
		echo '<input type="hidden" name="action" value="gowheels_record_payment"><input type="hidden" name="id" value="' . (int) $id . '">';
		echo '<select name="kind"><option value="payment">' . esc_html__( 'Payment received', 'gowheels' ) . '</option><option value="refund">' . esc_html__( 'Refund issued', 'gowheels' ) . '</option></select> ';
		echo '<select name="type"><option value="deposit">' . esc_html__( 'Deposit', 'gowheels' ) . '</option><option value="balance">' . esc_html__( 'Balance', 'gowheels' ) . '</option><option value="full">' . esc_html__( 'Full', 'gowheels' ) . '</option></select> ';
		echo '<select name="method"><option value="cash">' . esc_html__( 'Cash', 'gowheels' ) . '</option><option value="bank_transfer">' . esc_html__( 'Bank transfer', 'gowheels' ) . '</option><option value="mobile_banking">' . esc_html__( 'Mobile banking', 'gowheels' ) . '</option><option value="other">' . esc_html__( 'Other', 'gowheels' ) . '</option></select> ';
		echo '<input type="number" step="0.01" min="0.01" name="amount" placeholder="' . esc_attr__( 'Amount', 'gowheels' ) . '" required> <input type="text" name="note" placeholder="' . esc_attr__( 'Note (optional)', 'gowheels' ) . '" maxlength="255"> ';
		echo '<button class="button button-primary">' . esc_html__( 'Save', 'gowheels' ) . '</button></form></div></div></div>';

		// Right column.
		echo '<div class="gw-col gw-col-side"><div class="postbox"><h2 class="hndle">' . esc_html__( 'Actions', 'gowheels' ) . '</h2><div class="inside">';
		echo '<form method="post" action="' . $action . '">'; // phpcs:ignore WordPress.Security.EscapeOutput
		wp_nonce_field( 'gowheels_booking_' . $id );
		echo '<input type="hidden" name="action" value="gowheels_booking_action"><input type="hidden" name="id" value="' . (int) $id . '">';
		echo '<p><input type="text" class="widefat" name="note" maxlength="200" placeholder="' . esc_attr__( 'Optional note (included in email)', 'gowheels' ) . '"></p><p class="gw-actions">';
		$btn = function ( $do, $label, $cls = 'button' ) {
			echo '<button type="submit" name="do" value="' . esc_attr( $do ) . '" class="' . esc_attr( $cls ) . '">' . esc_html( $label ) . '</button> ';
		};
		if ( GoWheels_Booking_Status::can_transition( $st, 'approved' ) ) {
			$btn( 'approve', __( 'Approve', 'gowheels' ), 'button button-primary' );
		}
		if ( GoWheels_Booking_Status::can_transition( $st, 'rejected' ) ) {
			$btn( 'reject', __( 'Reject', 'gowheels' ) );
		}
		if ( GoWheels_Booking_Status::PENDING === $st ) {
			$btn( 'extend', __( 'Extend hold', 'gowheels' ) );
		}
		if ( GoWheels_Booking_Status::can_transition( $st, 'in_progress' ) ) {
			$btn( 'start', __( 'Mark picked up', 'gowheels' ) );
		}
		if ( GoWheels_Booking_Status::can_transition( $st, 'completed' ) ) {
			$btn( 'complete', __( 'Mark completed', 'gowheels' ) );
		}
		if ( GoWheels_Booking_Status::can_transition( $st, 'cancelled' ) ) {
			$btn( 'cancel', __( 'Cancel booking', 'gowheels' ), 'button button-link-delete' );
		}
		if ( ! GoWheels_Booking_Status::transitions()[ $st ] ) {
			echo esc_html__( 'No further actions.', 'gowheels' );
		}
		echo '</p></form></div></div>';

		echo '<div class="postbox"><h2 class="hndle">' . esc_html__( 'Internal notes', 'gowheels' ) . '</h2><div class="inside"><form method="post" action="' . $action . '">'; // phpcs:ignore WordPress.Security.EscapeOutput
		wp_nonce_field( 'gowheels_notes_' . $id );
		echo '<input type="hidden" name="action" value="gowheels_save_notes"><input type="hidden" name="id" value="' . (int) $id . '">';
		echo '<textarea name="admin_notes" rows="4" class="widefat">' . esc_textarea( $b->admin_notes ) . '</textarea><p><button class="button">' . esc_html__( 'Save notes', 'gowheels' ) . '</button></p></form></div></div>';

		echo '<div class="postbox"><h2 class="hndle">' . esc_html__( 'History', 'gowheels' ) . '</h2><div class="inside"><ul class="gw-events">';
		foreach ( $events as $e ) {
			$who = $e->actor_user_id ? get_the_author_meta( 'display_name', $e->actor_user_id ) : __( 'System / customer', 'gowheels' );
			printf( '<li><small>%s</small><br>%s <em>(%s)</em></li>', esc_html( GoWheels_Util::to_local( $e->created_at_utc ) ), esc_html( $e->message ), esc_html( $who ) );
		}
		echo '</ul></div></div></div></div>';
	}
}
