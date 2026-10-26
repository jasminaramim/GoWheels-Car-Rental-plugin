<?php
defined( 'ABSPATH' ) || exit;
/** @var array $view @var string $ref @var string $token @var string $paid @var int $hold */
?>
<div class="gw-wrap gw-status" data-ref="<?php echo esc_attr( $ref ); ?>" data-token="<?php echo esc_attr( $token ); ?>">
	<?php if ( '1' === $paid ) : ?>
		<p class="gw-notice gw-success" role="status"><?php esc_html_e( 'Thank you! We are confirming your payment — this page will update shortly.', 'gowheels' ); ?></p>
	<?php elseif ( '0' === $paid ) : ?>
		<p class="gw-notice" role="status"><?php esc_html_e( 'Payment was cancelled. You can try again below.', 'gowheels' ); ?></p>
	<?php endif; ?>
	<div class="gw-form-error" role="alert" aria-live="assertive" hidden></div>

	<h2><?php printf( /* translators: %s ref */ esc_html__( 'Booking %s', 'gowheels' ), esc_html( $view['ref'] ) ); ?></h2>
	<p>
		<?php echo GoWheels_Booking_Status::badge( $view['status'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo GoWheels_Booking_Status::badge( $view['payment_status'], true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</p>

	<?php if ( 'pending_review' === $view['status'] ) : ?>
		<p class="gw-notice"><?php esc_html_e( 'Your request has been received and is waiting for approval. It is not a confirmed booking yet.', 'gowheels' ); ?>
		<?php if ( $view['hold_expires'] ) : ?><?php printf( esc_html__( ' The car is held for you until %s.', 'gowheels' ), esc_html( $view['hold_expires'] ) ); ?><?php endif; ?></p>
	<?php elseif ( 'approved' === $view['status'] ) : ?>
		<p class="gw-notice gw-success"><?php esc_html_e( 'Your booking is approved.', 'gowheels' ); ?></p>
	<?php elseif ( 'expired' === $view['status'] ) : ?>
		<p class="gw-notice"><?php esc_html_e( 'The temporary hold has ended and the car is no longer reserved. We may still contact you if it is free.', 'gowheels' ); ?></p>
	<?php elseif ( in_array( $view['status'], array( 'rejected', 'cancelled' ), true ) ) : ?>
		<p class="gw-notice"><?php esc_html_e( 'This booking is closed.', 'gowheels' ); ?></p>
	<?php endif; ?>

	<table class="gw-table gw-kv">
		<tr><th><?php esc_html_e( 'Vehicle', 'gowheels' ); ?></th><td><?php echo esc_html( $view['vehicle'] ); ?></td></tr>
		<tr><th><?php esc_html_e( 'Pickup', 'gowheels' ); ?></th><td><?php echo esc_html( $view['pickup'] . ( $view['pickup_location'] ? ' — ' . $view['pickup_location'] : '' ) ); ?></td></tr>
		<tr><th><?php esc_html_e( 'Return', 'gowheels' ); ?></th><td><?php echo esc_html( $view['return'] . ( $view['return_location'] ? ' — ' . $view['return_location'] : '' ) ); ?></td></tr>
		<?php foreach ( $view['lines'] as $l ) : ?>
			<tr><th><?php echo esc_html( $l['label'] ); ?></th><td><?php echo esc_html( GoWheels_Util::money( $l['amount'] ) ); ?></td></tr>
		<?php endforeach; ?>
		<tr class="gw-total"><th><?php esc_html_e( 'Total', 'gowheels' ); ?></th><td><?php echo esc_html( GoWheels_Util::money( $view['grand_total'] ) ); ?></td></tr>
		<tr><th><?php esc_html_e( 'Deposit', 'gowheels' ); ?></th><td><?php echo esc_html( GoWheels_Util::money( $view['deposit_due'] ) ); ?></td></tr>
		<tr><th><?php esc_html_e( 'Paid', 'gowheels' ); ?></th><td><?php echo esc_html( GoWheels_Util::money( $view['paid'] ) ); ?></td></tr>
		<tr><th><?php esc_html_e( 'Balance due', 'gowheels' ); ?></th><td><?php echo esc_html( GoWheels_Util::money( $view['balance_due'] ) ); ?></td></tr>
	</table>

	<p class="gw-actions">
		<?php if ( $view['can_pay_deposit'] ) : ?>
			<button type="button" class="gw-btn" data-gw-pay="deposit"><?php printf( esc_html__( 'Pay deposit (%s)', 'gowheels' ), esc_html( GoWheels_Util::money( $view['deposit_remaining'] ) ) ); ?></button>
		<?php endif; ?>
		<?php if ( $view['can_pay'] ) : ?>
			<button type="button" class="gw-btn gw-btn-alt" data-gw-pay="balance"><?php printf( esc_html__( 'Pay full balance (%s)', 'gowheels' ), esc_html( GoWheels_Util::money( $view['balance_due'] ) ) ); ?></button>
		<?php endif; ?>
		<?php if ( $view['can_cancel'] ) : ?>
			<button type="button" class="gw-btn gw-btn-alt" data-gw-cancel><?php esc_html_e( 'Cancel booking', 'gowheels' ); ?></button>
		<?php endif; ?>
	</p>
</div>
