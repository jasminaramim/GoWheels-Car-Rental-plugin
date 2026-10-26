<?php
defined( 'ABSPATH' ) || exit;
/**
 * Horizontal Row Vehicle Card Template
 *
 * @var array      $car
 * @var array|null $quote
 * @var string     $book_url
 */
?>
<article class="gw-card gw-card-horizontal">
	<div class="gw-card-media-col">
		<?php if ( ! empty( $car['image'] ) ) : ?>
			<a class="gw-card-img" href="<?php echo esc_url( $car['permalink'] ); ?>" aria-label="<?php echo esc_attr( $car['title'] ); ?>">
				<img src="<?php echo esc_url( $car['image'] ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>" loading="lazy">
			</a>
		<?php else : ?>
			<div class="gw-card-img-placeholder">
				<svg class="gw-icon-car" viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.5l1.8-3.6A2 2 0 0 1 10.1 2h3.8a2 2 0 0 1 1.8 1.4L17.5 7H19a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17a2 2 0 1 0 4 0M15 17a2 2 0 1 0 4 0"/></svg>
			</div>
		<?php endif; ?>

		<div class="gw-card-badges">
			<?php if ( ! empty( $car['fuel_type'] ) ) : ?>
				<span class="gw-pill-badge gw-badge-fuel"><?php echo esc_html( $car['fuel_type'] ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="gw-card-info-col">
		<div class="gw-card-header">
			<h3 class="gw-card-title">
				<a href="<?php echo esc_url( $car['permalink'] ); ?>"><?php echo esc_html( $car['title'] ); ?></a>
			</h3>
			<?php if ( ! empty( $car['location_name'] ) ) : ?>
				<span class="gw-location-tag">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
					<?php echo esc_html( $car['location_name'] ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $car['excerpt'] ) ) : ?>
			<p class="gw-card-excerpt"><?php echo esc_html( wp_trim_words( $car['excerpt'], 18 ) ); ?></p>
		<?php endif; ?>

		<div class="gw-specs-chips">
			<?php if ( ! empty( $car['seats'] ) ) : ?>
				<span class="gw-spec-chip">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
					<?php printf( esc_html__( '%d Seats', 'gowheels' ), (int) $car['seats'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $car['transmission'] ) ) : ?>
				<span class="gw-spec-chip">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
					<?php echo esc_html( $car['transmission'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $car['luggage'] ) ) : ?>
				<span class="gw-spec-chip">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
					<?php printf( esc_html__( '%d Bags', 'gowheels' ), (int) $car['luggage'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $car['min_rental'] ) && $car['min_rental'] > 1 ) : ?>
				<span class="gw-spec-chip">
					<?php printf( esc_html__( 'Min %d days', 'gowheels' ), (int) $car['min_rental'] ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $car['features'] ) ) : ?>
			<div class="gw-feature-pills">
				<?php foreach ( array_slice( $car['features'], 0, 4 ) as $f ) : ?>
					<span class="gw-feature-pill">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
						<?php echo esc_html( $f ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="gw-card-action-col">
		<div class="gw-price-box">
			<span class="gw-price-label"><?php esc_html_e( 'From', 'gowheels' ); ?></span>
			<div class="gw-price-amount">
				<strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong>
				<span class="gw-price-unit"><?php esc_html_e( '/ day', 'gowheels' ); ?></span>
			</div>
			<?php if ( $quote ) : ?>
				<div class="gw-quote-callout">
					<?php esc_html_e( 'Total for dates:', 'gowheels' ); ?> <strong><?php echo esc_html( GoWheels_Util::money( $quote['grand_total'] ) ); ?></strong>
				</div>
			<?php endif; ?>
		</div>

		<div class="gw-action-buttons">
			<a class="gw-btn gw-btn-modern" href="<?php echo esc_url( $book_url ); ?>">
				<span><?php esc_html_e( 'Instant Book', 'gowheels' ); ?></span>
			</a>
			<a class="gw-btn-link" href="<?php echo esc_url( $car['permalink'] ); ?>">
				<?php esc_html_e( 'View Details →', 'gowheels' ); ?>
			</a>
		</div>
	</div>
</article>
