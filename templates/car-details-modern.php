<?php
defined( 'ABSPATH' ) || exit;
/**
 * Single Vehicle Template 1: Modern Split Showcase
 *
 * @var array  $car
 * @var string $book_url
 */
$main_image = ! empty( $car['image'] ) ? $car['image'] : ( ! empty( $car['gallery'][0]['url'] ) ? $car['gallery'][0]['url'] : '' );
?>
<div class="gw-wrap gw-details gw-details-modern">
	<div class="gw-details-grid">
		<!-- Left: Media & Highlights -->
		<div class="gw-details-media-col">
			<div class="gw-gallery-showcase">
				<div class="gw-main-image-wrap">
					<?php if ( $main_image ) : ?>
						<img id="gw-main-img" class="gw-main-img" src="<?php echo esc_url( $main_image ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>">
					<?php else : ?>
						<div class="gw-card-img-placeholder">
							<svg class="gw-icon-car" viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.5l1.8-3.6A2 2 0 0 1 10.1 2h3.8a2 2 0 0 1 1.8 1.4L17.5 7H19a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17a2 2 0 1 0 4 0M15 17a2 2 0 1 0 4 0"/></svg>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $car['gallery'] ) ) : ?>
					<div class="gw-thumbnail-strip" role="group" aria-label="<?php esc_attr_e( 'Car gallery thumbnails', 'gowheels' ); ?>">
						<?php if ( ! empty( $car['image'] ) ) : ?>
							<button type="button" class="gw-thumb-btn is-active" data-gw-full="<?php echo esc_url( $car['image'] ); ?>" aria-label="<?php esc_attr_e( 'Main image', 'gowheels' ); ?>">
								<img src="<?php echo esc_url( $car['image'] ); ?>" alt="">
							</button>
						<?php endif; ?>
						<?php foreach ( $car['gallery'] as $idx => $g ) : ?>
							<button type="button" class="gw-thumb-btn" data-gw-full="<?php echo esc_url( $g['url'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Gallery image %d', 'gowheels' ), $idx + 1 ) ); ?>">
								<img src="<?php echo esc_url( $g['url'] ); ?>" alt="<?php echo esc_attr( $g['alt'] ? $g['alt'] : '' ); ?>" loading="lazy">
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $car['excerpt'] ) ) : ?>
				<div class="gw-details-description">
					<h3><?php esc_html_e( 'About This Vehicle', 'gowheels' ); ?></h3>
					<p><?php echo esc_html( $car['excerpt'] ); ?></p>
				</div>
			<?php endif; ?>

			<!-- Trust Highlights -->
			<div class="gw-trust-bar">
				<div class="gw-trust-item">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
					<div>
						<strong><?php esc_html_e( 'Fully Insured', 'gowheels' ); ?></strong>
						<span><?php esc_html_e( 'Standard damage waiver included', 'gowheels' ); ?></span>
					</div>
				</div>
				<div class="gw-trust-item">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
					<div>
						<strong><?php esc_html_e( 'Instant Approval', 'gowheels' ); ?></strong>
						<span><?php esc_html_e( 'Guaranteed vehicle reservation', 'gowheels' ); ?></span>
					</div>
				</div>
				<div class="gw-trust-item">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
					<div>
						<strong><?php esc_html_e( '24/7 Road Support', 'gowheels' ); ?></strong>
						<span><?php esc_html_e( 'Assistance anywhere, anytime', 'gowheels' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Right: Sticky Booking & Spec Panel -->
		<div class="gw-details-sidebar-col">
			<div class="gw-booking-box gw-sticky-box">
				<div class="gw-booking-box-header">
					<div class="gw-title-wrap">
						<span class="gw-badge-pill"><?php echo ! empty( $car['fuel_type'] ) ? esc_html( $car['fuel_type'] ) : esc_html__( 'Premium Fleet', 'gowheels' ); ?></span>
						<h1 class="gw-details-title"><?php echo esc_html( $car['title'] ); ?></h1>
						<?php if ( ! empty( $car['location_name'] ) ) : ?>
							<p class="gw-loc-tag">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
								<?php echo esc_html( $car['location_name'] ); ?>
							</p>
						<?php endif; ?>
					</div>

					<div class="gw-price-banner">
						<div class="gw-price-main">
							<span class="gw-price-amount"><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></span>
							<span class="gw-price-period"><?php esc_html_e( '/ day', 'gowheels' ); ?></span>
						</div>
						<?php if ( ! empty( $car['price_hourly'] ) && $car['price_hourly'] > 0 ) : ?>
							<span class="gw-hourly-rate"><?php printf( esc_html__( 'or %s / hr', 'gowheels' ), esc_html( GoWheels_Util::money( $car['price_hourly'] ) ) ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<!-- Specs Matrix -->
				<div class="gw-specs-matrix">
					<?php if ( ! empty( $car['seats'] ) ) : ?>
						<div class="gw-spec-cell">
							<span class="gw-spec-icon">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
							</span>
							<div class="gw-spec-info">
								<span class="gw-spec-name"><?php esc_html_e( 'Passengers', 'gowheels' ); ?></span>
								<strong class="gw-spec-val"><?php printf( esc_html__( '%d Seats', 'gowheels' ), (int) $car['seats'] ); ?></strong>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $car['transmission'] ) ) : ?>
						<div class="gw-spec-cell">
							<span class="gw-spec-icon">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
							</span>
							<div class="gw-spec-info">
								<span class="gw-spec-name"><?php esc_html_e( 'Transmission', 'gowheels' ); ?></span>
								<strong class="gw-spec-val"><?php echo esc_html( $car['transmission'] ); ?></strong>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $car['fuel_type'] ) ) : ?>
						<div class="gw-spec-cell">
							<span class="gw-spec-icon">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17M15 11l4-2v8l-4-2M9 9h.01"/></svg>
							</span>
							<div class="gw-spec-info">
								<span class="gw-spec-name"><?php esc_html_e( 'Fuel Type', 'gowheels' ); ?></span>
								<strong class="gw-spec-val"><?php echo esc_html( $car['fuel_type'] ); ?></strong>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $car['luggage'] ) ) : ?>
						<div class="gw-spec-cell">
							<span class="gw-spec-icon">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
							</span>
							<div class="gw-spec-info">
								<span class="gw-spec-name"><?php esc_html_e( 'Luggage', 'gowheels' ); ?></span>
								<strong class="gw-spec-val"><?php printf( esc_html__( '%d Bags', 'gowheels' ), (int) $car['luggage'] ); ?></strong>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $car['min_rental'] ) && $car['min_rental'] > 1 ) : ?>
					<div class="gw-min-rental-alert">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
						<span><?php printf( esc_html__( 'Minimum booking duration is %d days.', 'gowheels' ), (int) $car['min_rental'] ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $car['features'] ) ) : ?>
					<div class="gw-details-features-box">
						<h4><?php esc_html_e( 'Included Features', 'gowheels' ); ?></h4>
						<ul class="gw-features-list">
							<?php foreach ( $car['features'] as $f ) : ?>
								<li>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
									<span><?php echo esc_html( $f ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="gw-cta-section">
					<a class="gw-btn gw-btn-large gw-btn-full" href="<?php echo esc_url( $book_url ); ?>">
						<span><?php esc_html_e( 'Book This Car Now', 'gowheels' ); ?></span>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
					</a>
					<p class="gw-cta-note"><?php esc_html_e( '⚡ Instant confirmation • Free cancellation options', 'gowheels' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
