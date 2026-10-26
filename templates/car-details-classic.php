<?php
defined( 'ABSPATH' ) || exit;
/**
 * Single Vehicle Template 3: Luxury Classic Showcase
 *
 * @var array  $car
 * @var string $book_url
 */
$main_image = ! empty( $car['image'] ) ? $car['image'] : ( ! empty( $car['gallery'][0]['url'] ) ? $car['gallery'][0]['url'] : '' );
?>
<div class="gw-wrap gw-details gw-details-classic">
	<div class="gw-classic-hero">
		<?php if ( $main_image ) : ?>
			<div class="gw-classic-img-wrap">
				<img src="<?php echo esc_url( $main_image ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>">
			</div>
		<?php endif; ?>

		<div class="gw-classic-header">
			<div class="gw-classic-header-left">
				<div class="gw-classic-badge-row">
					<span class="gw-badge-pill"><?php esc_html_e( 'Luxury Selection', 'gowheels' ); ?></span>
					<?php if ( ! empty( $car['fuel_type'] ) ) : ?>
						<span class="gw-badge-pill gw-badge-outline"><?php echo esc_html( $car['fuel_type'] ); ?></span>
					<?php endif; ?>
				</div>
				<h1 class="gw-classic-title"><?php echo esc_html( $car['title'] ); ?></h1>
				<?php if ( ! empty( $car['location_name'] ) ) : ?>
					<p class="gw-classic-loc">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
						<?php echo esc_html( $car['location_name'] ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="gw-classic-header-right">
				<div class="gw-classic-price-badge">
					<span class="gw-classic-price-num"><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></span>
					<span class="gw-classic-price-label"><?php esc_html_e( '/ 24 Hours', 'gowheels' ); ?></span>
				</div>
				<a class="gw-btn gw-btn-large gw-btn-modern" href="<?php echo esc_url( $book_url ); ?>">
					<?php esc_html_e( 'Reserve Vehicle', 'gowheels' ); ?>
				</a>
			</div>
		</div>
	</div>

	<!-- Specification Card Matrix (4 Cards) -->
	<div class="gw-classic-spec-grid">
		<div class="gw-classic-spec-card">
			<div class="gw-classic-spec-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
			</div>
			<div class="gw-classic-spec-content">
				<span class="gw-label"><?php esc_html_e( 'Transmission', 'gowheels' ); ?></span>
				<strong><?php echo esc_html( $car['transmission'] ? $car['transmission'] : '—' ); ?></strong>
			</div>
		</div>

		<div class="gw-classic-spec-card">
			<div class="gw-classic-spec-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
			</div>
			<div class="gw-classic-spec-content">
				<span class="gw-label"><?php esc_html_e( 'Passengers', 'gowheels' ); ?></span>
				<strong><?php printf( esc_html__( '%d Adult Seats', 'gowheels' ), (int) $car['seats'] ); ?></strong>
			</div>
		</div>

		<div class="gw-classic-spec-card">
			<div class="gw-classic-spec-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
			</div>
			<div class="gw-classic-spec-content">
				<span class="gw-label"><?php esc_html_e( 'Luggage Capacity', 'gowheels' ); ?></span>
				<strong><?php printf( esc_html__( '%d Large Bags', 'gowheels' ), (int) $car['luggage'] ); ?></strong>
			</div>
		</div>

		<div class="gw-classic-spec-card">
			<div class="gw-classic-spec-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17M15 11l4-2v8l-4-2M9 9h.01"/></svg>
			</div>
			<div class="gw-classic-spec-content">
				<span class="gw-label"><?php esc_html_e( 'Engine / Fuel', 'gowheels' ); ?></span>
				<strong><?php echo esc_html( $car['fuel_type'] ? $car['fuel_type'] : '—' ); ?></strong>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $car['features'] ) ) : ?>
		<div class="gw-classic-section">
			<h3 class="gw-section-title"><?php esc_html_e( 'Included Features & Comfort', 'gowheels' ); ?></h3>
			<ul class="gw-classic-features-list">
				<?php foreach ( $car['features'] as $f ) : ?>
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
						<span><?php echo esc_html( $f ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $car['gallery'] ) ) : ?>
		<div class="gw-classic-section">
			<h3 class="gw-section-title"><?php esc_html_e( 'Photo Gallery', 'gowheels' ); ?></h3>
			<div class="gw-classic-gallery">
				<?php foreach ( $car['gallery'] as $img ) : ?>
					<div class="gw-classic-gallery-thumb">
						<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ? $img['alt'] : $car['title'] ); ?>" loading="lazy">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="gw-classic-footer-cta">
		<div class="gw-cta-text">
			<h3><?php printf( esc_html__( 'Ready to drive the %s?', 'gowheels' ), esc_html( $car['title'] ) ); ?></h3>
			<p><?php esc_html_e( 'Guaranteed availability • No hidden charges • 100% Verified Fleet', 'gowheels' ); ?></p>
		</div>
		<a class="gw-btn gw-btn-large gw-btn-modern" href="<?php echo esc_url( $book_url ); ?>">
			<?php esc_html_e( 'Book This Car', 'gowheels' ); ?>
		</a>
	</div>
</div>
