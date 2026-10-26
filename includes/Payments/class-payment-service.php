<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Payments {

	public static function gateways() {
		static $list = null;
		if ( null === $list ) {
			$list = array();
			foreach ( apply_filters( 'gowheels_gateways', array( new GoWheels_Cash_Gateway(), new GoWheels_Stripe_Gateway() ) ) as $g ) {
				$list[ $g->id() ] = $g;
			}
		}
		return $list;
	}

	public static function gateway( $id ) {
		$all = self::gateways();
		return isset( $all[ $id ] ) ? $all[ $id ] : null;
	}

	public static function online_available() {
		$g = self::gateway( 'stripe' );
		return $g && $g->is_enabled();
	}

	public static function get( $id ) {
		global $wpdb;
		$t = GoWheels_Database::payments();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE id = %d", $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function for_booking( $booking_id ) {
		global $wpdb;
		$t = GoWheels_Database::payments();
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} WHERE booking_id = %d ORDER BY id ASC", $booking_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function sums( $booking_id ) {
		global $wpdb;
		$t    = GoWheels_Database::payments();
		$paid = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(amount),0) FROM {$t} WHERE booking_id = %d AND status = 'paid' AND payment_type <> 'refund'", $booking_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$ref  = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(amount),0) FROM {$t} WHERE booking_id = %d AND status = 'refunded' AND payment_type = 'refund'", $booking_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		return array( 'paid' => $paid, 'refunded' => $ref, 'net' => GoWheels_Util::round( $paid - $ref ) );
	}

	public static function net_paid( $booking_id ) {
		$s = self::sums( $booking_id );
		return $s['net'];
	}

	/** Recompute booking payment_status and balance_due from payment rows. */
	public static function recalc( $booking_id ) {
		global $wpdb;
		$b = GoWheels_Booking::get( $booking_id );
		if ( ! $b ) {
			return;
		}
		$t       = GoWheels_Database::payments();
		$s       = self::sums( $booking_id );
		$pending = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t} WHERE booking_id = %d AND status = 'pending'", $booking_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$failed  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t} WHERE booking_id = %d AND status = 'failed'", $booking_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		if ( $s['net'] <= 0 ) {
			if ( $s['refunded'] > 0 ) {
				$status = 'refunded';
			} elseif ( $pending ) {
				$status = 'pending';
			} elseif ( $failed ) {
				$status = 'failed';
			} else {
				$status = 'unpaid';
			}
		} elseif ( $s['net'] >= (float) $b->grand_total - 0.005 ) {
			$status = 'paid';
		} else {
			$status = 'partially_paid';
		}

		$wpdb->update(
			GoWheels_Database::bookings(),
			array(
				'payment_status' => $status,
				'balance_due'    => max( 0, GoWheels_Util::round( (float) $b->grand_total - $s['net'] ) ),
				'updated_at_utc' => GoWheels_Util::now_utc(),
			),
			array( 'id' => (int) $booking_id )
		);
	}

	/** Admin records an offline payment (cash, bank transfer, mobile banking…). */
	public static function add_manual( $booking_id, $amount, $type, $method, $note, $actor ) {
		global $wpdb;
		$b = GoWheels_Booking::get( $booking_id );
		if ( ! $b ) {
			return new WP_Error( 'gowheels_not_found', __( 'Booking not found.', 'gowheels' ) );
		}
		$amount = GoWheels_Util::round( $amount );
		$net    = self::net_paid( $booking_id );
		if ( $amount <= 0 ) {
			return new WP_Error( 'gowheels_amount', __( 'Enter an amount greater than zero.', 'gowheels' ) );
		}
		if ( $amount > (float) $b->grand_total - $net + 0.009 ) {
			return new WP_Error( 'gowheels_amount', __( 'Amount is more than the outstanding balance.', 'gowheels' ) );
		}
		$type   = in_array( $type, array( 'deposit', 'balance', 'full' ), true ) ? $type : 'deposit';
		$method = in_array( $method, array( 'cash', 'bank_transfer', 'mobile_banking', 'other' ), true ) ? $method : 'cash';
		$now    = GoWheels_Util::now_utc();

		$ok = $wpdb->insert(
			GoWheels_Database::payments(),
			array(
				'booking_id'      => (int) $booking_id,
				'gateway'         => $method,
				'payment_type'    => $type,
				'amount'          => $amount,
				'currency'        => $b->currency,
				'status'          => 'paid',
				'idempotency_key' => 'manual:' . wp_generate_uuid4(),
				'note'            => substr( sanitize_text_field( $note ), 0, 255 ),
				'created_at_utc'  => $now,
				'paid_at_utc'     => $now,
			)
		);
		if ( ! $ok ) {
			return new WP_Error( 'gowheels_db', __( 'Could not record the payment.', 'gowheels' ) );
		}
		self::recalc( $booking_id );
		GoWheels_Booking::log( $booking_id, 'payment_recorded', sprintf( '%s %s recorded (%s)', $type, $amount, $method ), $actor );
		GoWheels_Email::send( 'payment_received', GoWheels_Booking::get( $booking_id ), array( 'amount' => $amount ) );
		return true;
	}

	public static function refund( $booking_id, $amount, $note, $actor ) {
		global $wpdb;
		$b = GoWheels_Booking::get( $booking_id );
		if ( ! $b ) {
			return new WP_Error( 'gowheels_not_found', __( 'Booking not found.', 'gowheels' ) );
		}
		$amount = GoWheels_Util::round( $amount );
		if ( $amount <= 0 || $amount > self::net_paid( $booking_id ) + 0.009 ) {
			return new WP_Error( 'gowheels_amount', __( 'Refund must be between zero and the amount paid.', 'gowheels' ) );
		}
		$now = GoWheels_Util::now_utc();
		$wpdb->insert(
			GoWheels_Database::payments(),
			array(
				'booking_id'      => (int) $booking_id,
				'gateway'         => 'cash',
				'payment_type'    => 'refund',
				'amount'          => $amount,
				'currency'        => $b->currency,
				'status'          => 'refunded',
				'idempotency_key' => 'refund:' . wp_generate_uuid4(),
				'note'            => substr( sanitize_text_field( $note ), 0, 255 ),
				'created_at_utc'  => $now,
				'paid_at_utc'     => $now,
			)
		);
		self::recalc( $booking_id );
		GoWheels_Booking::log( $booking_id, 'refund_recorded', 'Refund ' . $amount . ' recorded', $actor );
		return true;
	}

	/** Amount payable online for a given type. */
	public static function amount_for( $booking, $type ) {
		$net = self::net_paid( $booking->id );
		if ( 'deposit' === $type ) {
			return max( 0, GoWheels_Util::round( (float) $booking->deposit_due - $net ) );
		}
		return max( 0, GoWheels_Util::round( (float) $booking->grand_total - $net ) );
	}

	/**
	 * Create a pending payment + hosted checkout session.
	 *
	 * @return array|WP_Error ['url' => ...]
	 */
	public static function start_online( $booking, $type = 'deposit' ) {
		global $wpdb;
		$gateway = self::gateway( 'stripe' );
		if ( ! $gateway || ! $gateway->is_enabled() ) {
			return new WP_Error( 'gowheels_online_off', __( 'Online payment is not available.', 'gowheels' ), array( 'status' => 400 ) );
		}
		if ( ! in_array( $booking->status, array( GoWheels_Booking_Status::APPROVED, GoWheels_Booking_Status::IN_PROGRESS ), true ) ) {
			return new WP_Error( 'gowheels_not_approved', __( 'You can pay once the booking has been approved.', 'gowheels' ), array( 'status' => 409 ) );
		}
		$type   = 'balance' === $type ? 'balance' : 'deposit';
		$amount = self::amount_for( $booking, $type );
		if ( $amount <= 0 ) {
			return new WP_Error( 'gowheels_nothing_due', __( 'Nothing is due for this payment type.', 'gowheels' ), array( 'status' => 400 ) );
		}

		// Supersede earlier unfinished sessions (a late webhook can still complete them).
		$t = GoWheels_Database::payments();
		$wpdb->query( $wpdb->prepare( "UPDATE {$t} SET status = 'failed' WHERE booking_id = %d AND status = 'pending' AND gateway = %s", $booking->id, 'stripe' ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		$wpdb->insert(
			$t,
			array(
				'booking_id'      => (int) $booking->id,
				'gateway'         => 'stripe',
				'payment_type'    => $type,
				'amount'          => $amount,
				'currency'        => $booking->currency,
				'status'          => 'pending',
				'idempotency_key' => 'stripe:' . wp_generate_uuid4(),
				'created_at_utc'  => GoWheels_Util::now_utc(),
			)
		);
		$payment = self::get( $wpdb->insert_id );

		$session = $gateway->create_session( $booking, $payment );
		if ( is_wp_error( $session ) ) {
			$wpdb->update( $t, array( 'status' => 'failed' ), array( 'id' => (int) $payment->id ) );
			self::recalc( $booking->id );
			return $session;
		}
		$wpdb->update( $t, array( 'gateway_transaction_id' => $session['transaction_id'] ), array( 'id' => (int) $payment->id ) );
		self::recalc( $booking->id );
		GoWheels_Booking::log( $booking->id, 'payment_started', 'Online ' . $type . ' payment started' );
		return array( 'url' => $session['url'] );
	}

	/** Idempotent: a payment is only ever credited once. */
	public static function mark_paid( $payment_id, $transaction_id = '' ) {
		global $wpdb;
		$t   = GoWheels_Database::payments();
		$now = GoWheels_Util::now_utc();
		$n   = $wpdb->query( $wpdb->prepare( "UPDATE {$t} SET status = 'paid', paid_at_utc = %s, gateway_transaction_id = %s WHERE id = %d AND status <> 'paid'", $now, $transaction_id, $payment_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		if ( ! $n ) {
			return false; // duplicate event or unknown payment.
		}
		$p = self::get( $payment_id );
		self::recalc( $p->booking_id );
		GoWheels_Booking::log( $p->booking_id, 'payment_received', 'Online payment of ' . $p->amount . ' confirmed by gateway' );
		GoWheels_Email::send( 'payment_received', GoWheels_Booking::get( $p->booking_id ), array( 'amount' => (float) $p->amount ) );
		return true;
	}

	public static function mark_failed( $payment_id ) {
		global $wpdb;
		$t = GoWheels_Database::payments();
		$wpdb->query( $wpdb->prepare( "UPDATE {$t} SET status = 'failed' WHERE id = %d AND status = 'pending'", $payment_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$p = self::get( $payment_id );
		if ( $p ) {
			self::recalc( $p->booking_id );
		}
	}
}
