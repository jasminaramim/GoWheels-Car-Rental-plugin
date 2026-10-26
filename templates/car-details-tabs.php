<?php
defined( 'ABSPATH' ) || exit;
/**
 * Single Vehicle Template 2: Tabbed Executive Design
 *
 * @var array  $car
 * @var string $book_url
 */
$main_image = ! empty( $car['image'] ) ? $car['image'] : ( ! empty( $car['gallery'][0]['url'] ) ? $car['gallery'][0]['url'] : '' );
$settings   = GoWheels_Settings::all();
?>
<div class="gw-wrap gw-details gw-details-tabs">
	<!-- Top Hero Summary Card -->
	<div class="gw-tabs-hero-card">
		<div class="gw-hero-media">
			<?php if ( $main_image ) : ?>
				<img id="gw-tabs-main-img" class="gw-tabs-main-img" src="<?php echo esc_url( $main_image ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>">
			<?php else : ?>
				<div class="gw-card-img-placeholder">
					<svg class="gw-icon-car" viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14M5 17a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.5l1.8-3.6A2 2 0 0 1 10.1 2h3.8a2 2 0 0 1 1.8 1.4L17.5 7H19a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2M5 17a2 2 0 1 0 4 0M15 17a2 2 0 1 0 4 0"/></svg>
				</div>
			<?php endif; ?>
		</div>

		<div class="gw-hero-info">
			<div class="gw-hero-badges">
				<?php if ( ! empty( $car['fuel_type'] ) ) : ?>
					<span class="gw-pill-badge gw-badge-fuel"><?php echo esc_html( $car['fuel_type'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $car['location_name'] ) ) : ?>
					<span class="gw-pill-badge gw-badge-loc"><?php echo esc_html( $car['location_name'] ); ?></span>
				<?php endif; ?>
			</div>

			<h1 class="gw-details-title"><?php echo esc_html( $car['title'] ); ?></h1>

			<div class="gw-quick-spec-row">
				<?php if ( ! empty( $car['seats'] ) ) : ?>
					<span><?php printf( esc_html__( '%d Seats', 'gowheels' ), (int) $car['seats'] ); ?></span>
					<span class="gw-sep">•</span>
				<?php endif; ?>
				<?php if ( ! empty( $car['transmission'] ) ) : ?>
					<span><?php echo esc_html( $car['transmission'] ); ?></span>
					<span class="gw-sep">•</span>
				<?php endif; ?>
				<?php if ( ! empty( $car['luggage'] ) ) : ?>
					<span><?php printf( esc_html__( '%d Bags', 'gowheels' ), (int) $car['luggage'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="gw-hero-price-action">
				<div class="gw-hero-rate">
					<span class="gw-rate-amount"><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></span>
					<span class="gw-rate-unit"><?php esc_html_e( '/ day', 'gowheels' ); ?></span>
				</div>
				<a class="gw-btn gw-btn-modern" href="<?php echo esc_url( $book_url ); ?>">
					<span><?php esc_html_e( 'Book Now', 'gowheels' ); ?></span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
				</a>
			</div>
		</div>
	</div>

	<!-- Tab Navigation -->
	<nav class="gw-tab-nav gw-details-tab-nav" role="tablist" aria-label="<?php esc_attr_e( 'Car Information Tabs', 'gowheels' ); ?>">
		<button type="button" role="tab" class="gw-tab-btn is-active" data-gw-tab="overview" aria-selected="true" aria-controls="gw-pane-overview">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
			<span><?php esc_html_e( 'Overview', 'gowheels' ); ?></span>
		</button>
		<button type="button" role="tab" class="gw-tab-btn" data-gw-tab="specs" aria-selected="false" aria-controls="gw-pane-specs">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
			<span><?php esc_html_e( 'Specifications', 'gowheels' ); ?></span>
		</button>
		<button type="button" role="tab" class="gw-tab-btn" data-gw-tab="features" aria-selected="false" aria-controls="gw-pane-features">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
			<span><?php esc_html_e( 'Features', 'gowheels' ); ?></span>
		</button>
		<button type="button" role="tab" class="gw-tab-btn" data-gw-tab="terms" aria-selected="false" aria-controls="gw-pane-terms">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
			<span><?php esc_html_e( 'Rental Terms', 'gowheels' ); ?></span>
		</button>
	</nav>

	<!-- Tab Content Panels -->
	<div class="gw-tab-panels gw-details-tab-panels">
		<!-- Tab 1: Overview -->
		<div id="gw-pane-overview" class="gw-tab-panel is-active" role="tabpanel">
			<?php if ( ! empty( $car['excerpt'] ) ) : ?>
				<div class="gw-section-block">
					<h3><?php esc_html_e( 'Vehicle Summary', 'gowheels' ); ?></h3>
					<p class="gw-lead-text"><?php echo esc_html( $car['excerpt'] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $car['gallery'] ) ) : ?>
				<div class="gw-section-block">
					<h3><?php esc_html_e( 'Vehicle Gallery', 'gowheels' ); ?></h3>
					<div class="gw-gallery-grid">
						<?php foreach ( $car['gallery'] as $img ) : ?>
							<a class="gw-gallery-item" href="<?php echo esc_url( $img['url'] ); ?>" target="_blank" rel="noopener">
								<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ? $img['alt'] : $car['title'] ); ?>" loading="lazy">
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Tab 2: Specifications -->
		<div id="gw-pane-specs" class="gw-tab-panel" role="tabpanel" hidden>
			<div class="gw-section-block">
				<h3><?php esc_html_e( 'Technical Details', 'gowheels' ); ?></h3>
				<table class="gw-table gw-table-specs">
					<tbody>
						<tr>
							<th><?php esc_html_e( 'Transmission', 'gowheels' ); ?></th>
							<td><?php echo esc_html( $car['transmission'] ? $car['transmission'] : '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Fuel Type', 'gowheels' ); ?></th>
							<td><?php echo esc_html( $car['fuel_type'] ? $car['fuel_type'] : '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Seating Capacity', 'gowheels' ); ?></th>
							<td><?php printf( esc_html__( '%d Passengers', 'gowheels' ), (int) $car['seats'] ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Luggage Space', 'gowheels' ); ?></th>
							<td><?php printf( esc_html__( '%d Standard Suitcases', 'gowheels' ), (int) $car['luggage'] ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Home Pickup Location', 'gowheels' ); ?></th>
							<td><?php echo esc_html( $car['location_name'] ? $car['location_name'] : __( 'All Locations', 'gowheels' ) ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Minimum Rental Period', 'gowheels' ); ?></th>
							<td><?php printf( esc_html__( '%d Day(s)', 'gowheels' ), (int) $car['min_rental'] ); ?></td>
						</tr>
						<?php if ( ! empty( $car['price_hourly'] ) && $car['price_hourly'] > 0 ) : ?>
							<tr>
								<th><?php esc_html_e( 'Hourly Rental Rate', 'gowheels' ); ?></th>
								<td><?php echo esc_html( GoWheels_Util::money( $car['price_hourly'] ) ); ?> / hour</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Tab 3: Included Features -->
		<div id="gw-pane-features" class="gw-tab-panel" role="tabpanel" hidden>
			<div class="gw-section-block">
				<h3><?php esc_html_e( 'Features & Amenities', 'gowheels' ); ?></h3>
				<?php if ( ! empty( $car['features'] ) ) : ?>
					<div class="gw-features-grid">
						<?php foreach ( $car['features'] as $f ) : ?>
							<div class="gw-feature-card">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
								<span><?php echo esc_html( $f ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="gw-note"><?php esc_html_e( 'Standard manufacturer equipment included.', 'gowheels' ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<!-- Tab 4: Terms & Policies -->
		<div id="gw-pane-terms" class="gw-tab-panel" role="tabpanel" hidden>
			<div class="gw-section-block">
				<h3><?php esc_html_e( 'Booking & Rental Policies', 'gowheels' ); ?></h3>
				<div class="gw-policy-box">
					<h4><?php esc_html_e( 'Cancellation Policy', 'gowheels' ); ?></h4>
					<p><?php echo esc_html( ! empty( $settings['cancellation_policy'] ) ? $settings['cancellation_policy'] : __( 'Free cancellation up to 24 hours before pickup.', 'gowheels' ) ); ?></p>
				</div>
				<div class="gw-policy-box">
					<h4><?php esc_html_e( 'Driver Requirements', 'gowheels' ); ?></h4>
					<ul>
						<li><?php esc_html_e( 'Valid driver license held for at least 1 year', 'gowheels' ); ?></li>
						<li><?php esc_html_e( 'National ID or Passport copy required at vehicle handover', 'gowheels' ); ?></li>
						<li><?php esc_html_e( 'Fuel policy: Full-to-Full or return at same level', 'gowheels' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- Bottom Action Bar -->
	<div class="gw-bottom-booking-bar">
		<div class="gw-bottom-rate">
			<strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong>
			<span><?php esc_html_e( '/ day', 'gowheels' ); ?></span>
		</div>
		<a class="gw-btn gw-btn-large" href="<?php echo esc_url( $book_url ); ?>">
			<?php esc_html_e( 'Reserve This Car', 'gowheels' ); ?>
		</a>
	</div>
</div>
