<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Booking_Status {

	const PENDING     = 'pending_review';
	const APPROVED    = 'approved';
	const REJECTED    = 'rejected';
	const CANCELLED   = 'cancelled';
	const IN_PROGRESS = 'in_progress';
	const COMPLETED   = 'completed';
	const EXPIRED     = 'expired';

	public static function labels() {
		return array(
			self::PENDING     => __( 'Pending review', 'gowheels' ),
			self::APPROVED    => __( 'Approved', 'gowheels' ),
			self::REJECTED    => __( 'Rejected', 'gowheels' ),
			self::CANCELLED   => __( 'Cancelled', 'gowheels' ),
			self::IN_PROGRESS => __( 'In progress', 'gowheels' ),
			self::COMPLETED   => __( 'Completed', 'gowheels' ),
			self::EXPIRED     => __( 'Hold expired', 'gowheels' ),
		);
	}

	public static function payment_labels() {
		return array(
			'unpaid'         => __( 'Unpaid', 'gowheels' ),
			'pending'        => __( 'Payment pending', 'gowheels' ),
			'partially_paid' => __( 'Partially paid', 'gowheels' ),
			'paid'           => __( 'Paid', 'gowheels' ),
			'failed'         => __( 'Payment failed', 'gowheels' ),
			'refunded'       => __( 'Refunded', 'gowheels' ),
		);
	}

	public static function label( $status ) {
		$l = self::labels();
		return isset( $l[ $status ] ) ? $l[ $status ] : $status;
	}

	public static function payment_label( $status ) {
		$l = self::payment_labels();
		return isset( $l[ $status ] ) ? $l[ $status ] : $status;
	}

	/** Statuses that always block the vehicle. */
	public static function blocking() {
		return array( self::APPROVED, self::IN_PROGRESS );
	}

	public static function transitions() {
		return array(
			self::PENDING     => array( self::APPROVED, self::REJECTED, self::CANCELLED, self::EXPIRED ),
			self::EXPIRED     => array( self::APPROVED, self::REJECTED ),
			self::APPROVED    => array( self::IN_PROGRESS, self::CANCELLED ),
			self::IN_PROGRESS => array( self::COMPLETED ),
			self::REJECTED    => array(),
			self::CANCELLED   => array(),
			self::COMPLETED   => array(),
		);
	}

	public static function can_transition( $from, $to ) {
		$t = self::transitions();
		return isset( $t[ $from ] ) && in_array( $to, $t[ $from ], true );
	}

	public static function badge( $status, $payment = false ) {
		$label = $payment ? self::payment_label( $status ) : self::label( $status );
		return '<span class="gw-badge gw-badge-' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
	}
}
