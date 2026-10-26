<?php
defined( 'ABSPATH' ) || exit;
/**
 * Design 3 — Compact Card Template
 *
 * Horizontal layout, compact image, vehicle information on one side
 * and green pricing with outline booking button on the other.
 *
 * @var array      $car
 * @var array|null $quote
 * @var string     $book_url
 */

$rating  = ! empty( $car['rating'] ) ? (float) $car['rating'] : 4.8;
$reviews = ! empty( $car['reviews_count'] ) ? (int) $car['reviews_count'] : 120;
$badge   = ! empty( $car['badge'] ) ? $car['badge'] : ( $car['price_daily'] > 80 ? 'Popular' : ( $car['price_daily'] < 70 ? 'Best Value' : 'Luxury' ) );
?>
<article class="gw-card gw-card-compact">
	<div class="gw-compact-row">
		<div class="gw-compact-media">
			<span class="gw-pill-badge gw-badge-compact"><?php echo esc_html( $badge ); ?></span>
			<?php if ( ! empty( $car['image'] ) ) : ?>
				<a class="gw-compact-img" href="<?php echo esc_url( $car['permalink'] ); ?>" aria-label="<?php echo esc_attr( $car['title'] ); ?>">
					<img src="<?php echo esc_url( $car['image'] ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>" loading="lazy">
				</a>
			<?php else : ?>
				<div class="gw-compact-img-placeholder">
					<svg class="gw-icon-car" viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.5l1.8-3.6A2 2 0 0 1 10.1 2h3.8a2 2 0 0 1 1.8 1.4L17.5 7H19a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17a2 2 0 1 0 4 0M15 17a2 2 0 1 0 4 0"/></svg>
				</div>
			<?php endif; ?>
		</div>

		<div class="gw-compact-info">
			<h3 class="gw-card-title">
				<a href="<?php echo esc_url( $car['permalink'] ); ?>"><?php echo esc_html( $car['title'] ); ?></a>
			</h3>
			<div class="gw-compact-sub">
				<?php echo ! empty( $car['location_name'] ) ? esc_html( $car['location_name'] ) : ( ! empty( $car['seats'] ) ? sprintf( esc_html__( 'Sedan · %d Seats', 'gowheels' ), (int) $car['seats'] ) : esc_html__( 'Verified Rental', 'gowheels' ) ); ?>
			</div>
			<div class="gw-compact-rating">
				<span class="gw-star-icon">★</span> <?php echo esc_html( number_format( $rating, 1 ) ); ?>
				<span class="gw-reviews-count">(<?php printf( esc_html__( '%d reviews', 'gowheels' ), $reviews ); ?>)</span>
			</div>
			<div class="gw-compact-specs">
				<?php if ( ! empty( $car['transmission'] ) ) : ?>
					<span>⚙ <?php echo esc_html( $car['transmission'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $car['fuel_type'] ) ) : ?>
					<span>⛽ <?php echo esc_html( $car['fuel_type'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $car['seats'] ) ) : ?>
					<span>👤 <?php printf( esc_html__( '%d Seats', 'gowheels' ), (int) $car['seats'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<div class="gw-compact-actions">
			<div class="gw-compact-price">
				<strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong> <span><?php esc_html_e( '/day', 'gowheels' ); ?></span>
			</div>
			<?php if ( $quote ) : ?>
				<div class="gw-quote-compact">
					<?php esc_html_e( 'Total:', 'gowheels' ); ?> <strong><?php echo esc_html( GoWheels_Util::money( $quote['grand_total'] ) ); ?></strong>
				</div>
			<?php endif; ?>
			<a class="gw-btn gw-btn-compact-outline" href="<?php echo esc_url( $book_url ); ?>">
				<span><?php esc_html_e( 'Book Now', 'gowheels' ); ?></span>
				<span class="gw-arrow-gt">&gt;</span>
			</a>
		</div>
	</div>
</article>
