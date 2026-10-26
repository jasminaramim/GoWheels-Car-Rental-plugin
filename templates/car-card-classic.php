<?php
defined( 'ABSPATH' ) || exit;
/**
 * Design 2 — Classic Card Template
 *
 * Detailed & Informative card layout with ratings, features checklist,
 * full specifications, and prominent booking button.
 *
 * @var array      $car
 * @var array|null $quote
 * @var string     $book_url
 */

$rating   = ! empty( $car['rating'] ) ? (float) $car['rating'] : 4.8;
$reviews  = ! empty( $car['reviews_count'] ) ? (int) $car['reviews_count'] : 120;
$features = ! empty( $car['features'] ) ? $car['features'] : array( 'Full AC', 'Heated Seats', 'Bluetooth', 'GPS Navigation' );
?>
<article class="gw-card gw-card-classic">
	<div class="gw-card-media-wrap">
		<div class="gw-rating-pill">
			<span class="gw-star-icon">★</span> <?php echo esc_html( number_format( $rating, 1 ) ); ?>
		</div>
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
		<div class="gw-classic-header">
			<div class="gw-classic-title-group">
				<h3 class="gw-card-title">
					<a href="<?php echo esc_url( $car['permalink'] ); ?>"><?php echo esc_html( $car['title'] ); ?></a>
				</h3>
				<div class="gw-classic-sub">
					<?php echo ! empty( $car['location_name'] ) ? esc_html( $car['location_name'] ) : ( ! empty( $car['seats'] ) ? sprintf( esc_html__( 'SUV | %d Seats', 'gowheels' ), (int) $car['seats'] ) : esc_html__( 'Premium Rental', 'gowheels' ) ); ?>
				</div>
			</div>
			<div class="gw-classic-price">
				<strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong><span><?php esc_html_e( '/day', 'gowheels' ); ?></span>
			</div>
		</div>

		<div class="gw-classic-specs">
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

		<div class="gw-classic-reviews">
			<span class="gw-star-icon">★</span> <?php echo esc_html( number_format( $rating, 1 ) ); ?>
			<span class="gw-reviews-count">(<?php printf( esc_html__( '%d reviews', 'gowheels' ), $reviews ); ?>)</span>
		</div>

		<div class="gw-classic-features-box">
			<span class="gw-features-lbl"><?php esc_html_e( 'Key Features', 'gowheels' ); ?></span>
			<div class="gw-features-grid">
				<?php foreach ( array_slice( $features, 0, 4 ) as $feat ) : ?>
					<div class="gw-feat-item">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
						<span><?php echo esc_html( $feat ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( $quote ) : ?>
			<div class="gw-quote-callout">
				<?php esc_html_e( 'Total for your dates:', 'gowheels' ); ?> <strong><?php echo esc_html( GoWheels_Util::money( $quote['grand_total'] ) ); ?></strong>
			</div>
		<?php endif; ?>

		<a class="gw-btn gw-btn-classic" href="<?php echo esc_url( $book_url ); ?>">
			<?php esc_html_e( 'Book Now', 'gowheels' ); ?>
		</a>
	</div>
</article>
