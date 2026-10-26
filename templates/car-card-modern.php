<?php
defined( 'ABSPATH' ) || exit;
/**
 * Modern Vehicle Card Template
 *
 * @var array      $car
 * @var array|null $quote
 * @var string     $book_url
 */
?>
<article class="gw-card gw-card-modern">
	<div class="gw-card-media-wrap">
		<?php 
		$badge = ! empty( $car['badge'] ) ? $car['badge'] : ( $car['price_daily'] > 80 ? __( 'Popular', 'gowheels' ) : ( $car['price_daily'] < 70 ? __( 'Best Value', 'gowheels' ) : __( 'Luxury', 'gowheels' ) ) );
		?>
		<span class="gw-pill-badge gw-badge-modern-pill"><?php echo esc_html( $badge ); ?></span>
		<button type="button" class="gw-wishlist-heart" aria-label="<?php esc_attr_e( 'Save to wishlist', 'gowheels' ); ?>">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
		</button>
		<?php if ( ! empty( $car['image'] ) ) : ?>
			<a class="gw-card-img" href="<?php echo esc_url( $car['permalink'] ); ?>" aria-label="<?php echo esc_attr( $car['title'] ); ?>">
				<img src="<?php echo esc_url( $car['image'] ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>" loading="lazy">
			</a>
		<?php else : ?>
			<div class="gw-card-img-placeholder">
				<svg class="gw-icon-car" viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.5l1.8-3.6A2 2 0 0 1 10.1 2h3.8a2 2 0 0 1 1.8 1.4L17.5 7H19a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17a2 2 0 1 0 4 0M15 17a2 2 0 1 0 4 0"/></svg>
			</div>
		<?php endif; ?>
	</div>

	<div class="gw-card-body">
		<div class="gw-card-header">
			<h3 class="gw-card-title">
				<a href="<?php echo esc_url( $car['permalink'] ); ?>"><?php echo esc_html( $car['title'] ); ?></a>
			</h3>
			<?php if ( ! empty( $car['min_rental'] ) && $car['min_rental'] > 1 ) : ?>
				<span class="gw-min-badge"><?php printf( esc_html__( 'Min %d days', 'gowheels' ), (int) $car['min_rental'] ); ?></span>
			<?php endif; ?>
		</div>

		<div class="gw-specs-chips">
			<?php if ( ! empty( $car['seats'] ) ) : ?>
				<span class="gw-spec-chip" title="<?php esc_attr_e( 'Passenger Capacity', 'gowheels' ); ?>">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
					<?php printf( esc_html__( '%d Seats', 'gowheels' ), (int) $car['seats'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $car['transmission'] ) ) : ?>
				<span class="gw-spec-chip" title="<?php esc_attr_e( 'Transmission', 'gowheels' ); ?>">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
					<?php echo esc_html( $car['transmission'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $car['luggage'] ) ) : ?>
				<span class="gw-spec-chip" title="<?php esc_attr_e( 'Luggage Capacity', 'gowheels' ); ?>">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
					<?php printf( esc_html__( '%d Bags', 'gowheels' ), (int) $car['luggage'] ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $car['features'] ) ) : ?>
			<div class="gw-feature-pills">
				<?php foreach ( array_slice( $car['features'], 0, 3 ) as $f ) : ?>
					<span class="gw-feature-pill">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
						<?php echo esc_html( $f ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="gw-card-footer">
			<div class="gw-price-box">
				<span class="gw-price-label"><?php esc_html_e( 'Daily Rate', 'gowheels' ); ?></span>
				<div class="gw-price-amount">
					<strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong>
					<span class="gw-price-unit"><?php esc_html_e( '/ day', 'gowheels' ); ?></span>
				</div>
				<?php if ( $quote ) : ?>
					<div class="gw-quote-callout">
						<?php esc_html_e( 'Total:', 'gowheels' ); ?> <strong><?php echo esc_html( GoWheels_Util::money( $quote['grand_total'] ) ); ?></strong>
					</div>
				<?php endif; ?>
			</div>

			<a class="gw-btn gw-btn-modern" href="<?php echo esc_url( $book_url ); ?>">
				<span><?php esc_html_e( 'Book Now', 'gowheels' ); ?></span>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
			</a>
		</div>
	</div>
</article>
