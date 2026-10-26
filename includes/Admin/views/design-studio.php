<?php
defined( 'ABSPATH' ) || exit;

/**
 * Design Studio Admin View
 *
 * @var array $vehicles
 */

$default_card   = (string) GoWheels_Settings::get( 'default_card_design' );
if ( empty( $default_card ) ) {
	$default_card = '1';
}

$default_single = (string) GoWheels_Settings::get( 'default_single_design' );
if ( empty( $default_single ) ) {
	$default_single = '1';
}

$first_vid = ! empty( $vehicles ) ? (string) $vehicles[0]['id'] : '123';
$img_base  = GOWHEELS_URL . 'assets/images/';
?>
<div class="wrap gw-design-studio-wrap">

	<!-- Header Banner matching Reference Screenshot -->
	<div class="gw-studio-header">
		<div class="gw-studio-brand-group">
			<div class="gw-studio-logo">
				<span class="dashicons dashicons-car"></span>
				<div class="gw-studio-logo-text">
					<h2>GoWheels</h2>
					<span>Car Rental for WordPress</span>
				</div>
			</div>
			<div class="gw-studio-title-box">
				<h1><?php esc_html_e( '3 Preset Card Designs', 'gowheels' ); ?></h1>
				<p>
					<?php esc_html_e( 'Use the', 'gowheels' ); ?> <strong>[gowheels_cars]</strong> <?php esc_html_e( 'shortcode with the design attribute to choose your favorite card layout. No coding needed!', 'gowheels' ); ?>
				</p>
			</div>
		</div>

		<div class="gw-studio-example-card">
			<span class="gw-example-lbl"><?php esc_html_e( 'Shortcode Example', 'gowheels' ); ?></span>
			<div class="gw-example-code">
				<code>[gowheels_cars design="1"]</code>
			</div>
			<span class="gw-example-hint"><?php esc_html_e( 'Replace 1 with 2 or 3 for other designs.', 'gowheels' ); ?></span>
		</div>
	</div>

	<!-- Section Switcher Tabs -->
	<nav class="gw-studio-tabs" role="tablist">
		<button type="button" class="gw-studio-tab-btn active" data-section="card-designs">
			<span class="dashicons dashicons-grid-view"></span>
			<?php esc_html_e( 'Car Card Designs (3 Presets)', 'gowheels' ); ?>
		</button>
		<button type="button" class="gw-studio-tab-btn" data-section="single-designs">
			<span class="dashicons dashicons-format-gallery"></span>
			<?php esc_html_e( 'Single Rental Designs (3 Presets)', 'gowheels' ); ?>
		</button>
	</nav>

	<!-- ============================================================== -->
	<!-- SECTION 1: CAR CARD DESIGNS (3 PRESETS)                        -->
	<!-- ============================================================== -->
	<div id="gw-section-card-designs" class="gw-studio-section active">

		<!-- DESIGN 1: Modern Card (Default) -->
		<div class="gw-preset-row <?php echo '1' === $default_card ? 'gw-preset-selected' : ''; ?>" id="gw-card-preset-1">
			<div class="gw-preset-sidebar">
				<div class="gw-num-circle gw-num-blue">1</div>
				<div class="gw-sidebar-info">
					<div class="gw-title-with-pill">
						<h3><?php esc_html_e( 'Design 1 – Modern Card (Default)', 'gowheels' ); ?></h3>
						<span class="gw-badge-pill gw-pill-cyan"><?php esc_html_e( 'Clean & Modern', 'gowheels' ); ?></span>
					</div>
					<p class="gw-preset-explanation">
						<?php esc_html_e( 'A clean and minimal card design with a focus on image, key details and a clear book button. Perfect for a modern website.', 'gowheels' ); ?>
					</p>

					<ul class="gw-checklist">
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Large image with overlay badges', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Key info (seats, transmission, fuel type)', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Daily price and book button', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Responsive and minimal layout', 'gowheels' ); ?></li>
					</ul>

					<div class="gw-shortcode-preview-box">
						<span class="gw-sc-lbl"><?php esc_html_e( 'Shortcode', 'gowheels' ); ?></span>
						<div class="gw-code-copy-row">
							<code>[gowheels_cars design="1"]</code>
							<button type="button" class="gw-btn-icon gw-copy-btn" data-copy='[gowheels_cars design="1"]' title="<?php esc_attr_e( 'Copy Shortcode', 'gowheels' ); ?>">
								<span class="dashicons dashicons-clipboard"></span>
							</button>
						</div>
					</div>

					<div class="gw-select-action-area">
						<button type="button" class="button gw-select-design-btn <?php echo '1' === $default_card ? 'gw-btn-selected' : 'button-primary'; ?>" data-type="card" data-id="1">
							<?php echo '1' === $default_card ? esc_html__( 'Selected ✓', 'gowheels' ) : esc_html__( 'Choose design', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="gw-preset-preview-panel">
				<div class="gw-cards-preview-grid">

					<!-- Card 1: Toyota Fortuner -->
					<div class="gw-demo-card gw-demo-modern">
						<div class="gw-demo-media">
							<span class="gw-demo-badge gw-badge-blue">Popular</span>
							<span class="gw-demo-heart">♡</span>
							<img src="<?php echo esc_url( $img_base . 'fortuner.jpg' ); ?>" alt="Toyota Fortuner">
						</div>
						<div class="gw-demo-body">
							<h4>Toyota Fortuner</h4>
							<span class="gw-demo-sub">SUV · 7 Seats</span>
							<div class="gw-demo-specs">
								<span>⚙ Automatic</span>
								<span>⛽ Diesel</span>
								<span>👤 7 Seats</span>
							</div>
							<div class="gw-demo-footer">
								<div class="gw-demo-price-blue">
									<strong>$89</strong> <span>/day</span>
								</div>
								<span class="gw-demo-btn-blue">Book Now →</span>
							</div>
						</div>
					</div>

					<!-- Card 2: BMW 3 Series -->
					<div class="gw-demo-card gw-demo-modern">
						<div class="gw-demo-media">
							<span class="gw-demo-badge gw-badge-green">Best Value</span>
							<span class="gw-demo-heart">♡</span>
							<img src="<?php echo esc_url( $img_base . 'bmw.jpg' ); ?>" alt="BMW 3 Series">
						</div>
						<div class="gw-demo-body">
							<h4>BMW 3 Series</h4>
							<span class="gw-demo-sub">Sedan · 5 Seats</span>
							<div class="gw-demo-specs">
								<span>⚙ Automatic</span>
								<span>⛽ Petrol</span>
								<span>👤 5 Seats</span>
							</div>
							<div class="gw-demo-footer">
								<div class="gw-demo-price-blue">
									<strong>$69</strong> <span>/day</span>
								</div>
								<span class="gw-demo-btn-blue">Book Now →</span>
							</div>
						</div>
					</div>

					<!-- Card 3: Mercedes C-Class -->
					<div class="gw-demo-card gw-demo-modern">
						<div class="gw-demo-media">
							<span class="gw-demo-badge gw-badge-purple">Luxury</span>
							<span class="gw-demo-heart">♡</span>
							<img src="<?php echo esc_url( $img_base . 'mercedes.jpg' ); ?>" alt="Mercedes C-Class">
						</div>
						<div class="gw-demo-body">
							<h4>Mercedes C-Class</h4>
							<span class="gw-demo-sub">Sedan · 5 Seats</span>
							<div class="gw-demo-specs">
								<span>⚙ Automatic</span>
								<span>⛽ Petrol</span>
								<span>👤 5 Seats</span>
							</div>
							<div class="gw-demo-footer">
								<div class="gw-demo-price-blue">
									<strong>$79</strong> <span>/day</span>
								</div>
								<span class="gw-demo-btn-blue">Book Now →</span>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<!-- DESIGN 2: Classic Card -->
		<div class="gw-preset-row <?php echo '2' === $default_card ? 'gw-preset-selected' : ''; ?>" id="gw-card-preset-2">
			<div class="gw-preset-sidebar">
				<div class="gw-num-circle gw-num-green">2</div>
				<div class="gw-sidebar-info">
					<div class="gw-title-with-pill">
						<h3><?php esc_html_e( 'Design 2 – Classic Card', 'gowheels' ); ?></h3>
						<span class="gw-badge-pill gw-pill-green"><?php esc_html_e( 'Detailed & Informative', 'gowheels' ); ?></span>
					</div>
					<p class="gw-preset-explanation">
						<?php esc_html_e( 'A classic layout with more information, including ratings, features and a clean structure. Ideal for showing more details at a glance.', 'gowheels' ); ?>
					</p>

					<ul class="gw-checklist">
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Image with rating and price badge', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Full specifications and features', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Description and extra info', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Clean and structured layout', 'gowheels' ); ?></li>
					</ul>

					<div class="gw-shortcode-preview-box">
						<span class="gw-sc-lbl"><?php esc_html_e( 'Shortcode', 'gowheels' ); ?></span>
						<div class="gw-code-copy-row">
							<code>[gowheels_cars design="2"]</code>
							<button type="button" class="gw-btn-icon gw-copy-btn" data-copy='[gowheels_cars design="2"]' title="<?php esc_attr_e( 'Copy Shortcode', 'gowheels' ); ?>">
								<span class="dashicons dashicons-clipboard"></span>
							</button>
						</div>
					</div>

					<div class="gw-select-action-area">
						<button type="button" class="button gw-select-design-btn <?php echo '2' === $default_card ? 'gw-btn-selected' : 'button-primary'; ?>" data-type="card" data-id="2">
							<?php echo '2' === $default_card ? esc_html__( 'Selected ✓', 'gowheels' ) : esc_html__( 'Choose design', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="gw-preset-preview-panel">
				<div class="gw-cards-preview-grid">

					<!-- Card 1: Toyota Fortuner -->
					<div class="gw-demo-card gw-demo-classic">
						<div class="gw-demo-media">
							<span class="gw-demo-badge-rating">4.8 ★</span>
							<img src="<?php echo esc_url( $img_base . 'fortuner.jpg' ); ?>" alt="Toyota Fortuner">
						</div>
						<div class="gw-demo-body">
							<div class="gw-classic-head-row">
								<div>
									<h4>Toyota Fortuner</h4>
									<span class="gw-demo-sub">SUV | 7 Seats</span>
								</div>
								<div class="gw-demo-price-green">$89<span>/day</span></div>
							</div>
							<div class="gw-demo-specs">
								<span>⚙ Automatic</span>
								<span>⛽ Diesel</span>
								<span>👤 7 Seats</span>
							</div>
							<div class="gw-demo-rating-line">
								⭐ 4.8 (120 reviews)
							</div>
							<div class="gw-demo-features-box">
								<span class="gw-feat-head">Key Features</span>
								<div class="gw-feat-items">
									<span>✓ Full AC</span>
									<span>✓ Heated Seats</span>
									<span>✓ Bluetooth</span>
									<span>✓ GPS Navigation</span>
								</div>
							</div>
							<span class="gw-demo-btn-green">Book Now</span>
						</div>
					</div>

					<!-- Card 2: BMW 3 Series -->
					<div class="gw-demo-card gw-demo-classic">
						<div class="gw-demo-media">
							<span class="gw-demo-badge-rating">4.7 ★</span>
							<img src="<?php echo esc_url( $img_base . 'bmw.jpg' ); ?>" alt="BMW 3 Series">
						</div>
						<div class="gw-demo-body">
							<div class="gw-classic-head-row">
								<div>
									<h4>BMW 3 Series</h4>
									<span class="gw-demo-sub">Sedan | 5 Seats</span>
								</div>
								<div class="gw-demo-price-green">$69<span>/day</span></div>
							</div>
							<div class="gw-demo-specs">
								<span>⚙ Automatic</span>
								<span>⛽ Petrol</span>
								<span>👤 5 Seats</span>
							</div>
							<div class="gw-demo-rating-line">
								⭐ 4.7 (98 reviews)
							</div>
							<div class="gw-demo-features-box">
								<span class="gw-feat-head">Key Features</span>
								<div class="gw-feat-items">
									<span>✓ Full AC</span>
									<span>✓ Sunroof</span>
									<span>✓ Bluetooth</span>
									<span>✓ Parking Sensors</span>
								</div>
							</div>
							<span class="gw-demo-btn-green">Book Now</span>
						</div>
					</div>

					<!-- Card 3: Mercedes C-Class -->
					<div class="gw-demo-card gw-demo-classic">
						<div class="gw-demo-media">
							<span class="gw-demo-badge-rating">4.9 ★</span>
							<img src="<?php echo esc_url( $img_base . 'mercedes.jpg' ); ?>" alt="Mercedes C-Class">
						</div>
						<div class="gw-demo-body">
							<div class="gw-classic-head-row">
								<div>
									<h4>Mercedes C-Class</h4>
									<span class="gw-demo-sub">Sedan | 5 Seats</span>
								</div>
								<div class="gw-demo-price-green">$79<span>/day</span></div>
							</div>
							<div class="gw-demo-specs">
								<span>⚙ Automatic</span>
								<span>⛽ Petrol</span>
								<span>👤 5 Seats</span>
							</div>
							<div class="gw-demo-rating-line">
								⭐ 4.9 (145 reviews)
							</div>
							<div class="gw-demo-features-box">
								<span class="gw-feat-head">Key Features</span>
								<div class="gw-feat-items">
									<span>✓ Full AC</span>
									<span>✓ Leather Seats</span>
									<span>✓ Bluetooth</span>
									<span>✓ GPS Navigation</span>
								</div>
							</div>
							<span class="gw-demo-btn-green">Book Now</span>
						</div>
					</div>

				</div>
			</div>
		</div>

		<!-- DESIGN 3: Compact Card -->
		<div class="gw-preset-row <?php echo '3' === $default_card ? 'gw-preset-selected' : ''; ?>" id="gw-card-preset-3">
			<div class="gw-preset-sidebar">
				<div class="gw-num-circle gw-num-purple">3</div>
				<div class="gw-sidebar-info">
					<div class="gw-title-with-pill">
						<h3><?php esc_html_e( 'Design 3 – Compact Card', 'gowheels' ); ?></h3>
						<span class="gw-badge-pill gw-pill-purple"><?php esc_html_e( 'Stylish & Compact', 'gowheels' ); ?></span>
					</div>
					<p class="gw-preset-explanation">
						<?php esc_html_e( 'A compact and stylish design with a horizontal layout. Best for a clean look with a modern feel.', 'gowheels' ); ?>
					</p>

					<ul class="gw-checklist">
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Horizontal layout', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Compact card structure', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Icon-based feature display', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Simple and elegant', 'gowheels' ); ?></li>
					</ul>

					<div class="gw-shortcode-preview-box">
						<span class="gw-sc-lbl"><?php esc_html_e( 'Shortcode', 'gowheels' ); ?></span>
						<div class="gw-code-copy-row">
							<code>[gowheels_cars design="3"]</code>
							<button type="button" class="gw-btn-icon gw-copy-btn" data-copy='[gowheels_cars design="3"]' title="<?php esc_attr_e( 'Copy Shortcode', 'gowheels' ); ?>">
								<span class="dashicons dashicons-clipboard"></span>
							</button>
						</div>
					</div>

					<div class="gw-select-action-area">
						<button type="button" class="button gw-select-design-btn <?php echo '3' === $default_card ? 'gw-btn-selected' : 'button-primary'; ?>" data-type="card" data-id="3">
							<?php echo '3' === $default_card ? esc_html__( 'Selected ✓', 'gowheels' ) : esc_html__( 'Choose design', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="gw-preset-preview-panel">
				<div class="gw-compact-preview-list">

					<!-- Row 1: Toyota Fortuner -->
					<div class="gw-demo-compact-row">
						<div class="gw-demo-compact-media">
							<span class="gw-demo-badge gw-badge-blue">Popular</span>
							<img src="<?php echo esc_url( $img_base . 'fortuner.jpg' ); ?>" alt="Toyota Fortuner">
						</div>
						<div class="gw-demo-compact-info">
							<h4>Toyota Fortuner</h4>
							<span class="gw-demo-sub">SUV · 7 Seats</span>
							<div class="gw-demo-rating-mini">⭐ 4.8 (120 reviews)</div>
							<div class="gw-demo-specs-mini">
								<span>⚙ Auto</span>
								<span>⛽ Diesel</span>
								<span>👤 7 Seats</span>
							</div>
						</div>
						<div class="gw-demo-compact-actions">
							<div class="gw-demo-price-green">
								<strong>$89</strong> <span>/day</span>
							</div>
							<span class="gw-demo-btn-outline-green">Book Now &gt;</span>
						</div>
					</div>

					<!-- Row 2: BMW 3 Series -->
					<div class="gw-demo-compact-row">
						<div class="gw-demo-compact-media">
							<span class="gw-demo-badge gw-badge-green">Best Value</span>
							<img src="<?php echo esc_url( $img_base . 'bmw.jpg' ); ?>" alt="BMW 3 Series">
						</div>
						<div class="gw-demo-compact-info">
							<h4>BMW 3 Series</h4>
							<span class="gw-demo-sub">Sedan · 5 Seats</span>
							<div class="gw-demo-rating-mini">⭐ 4.7 (98 reviews)</div>
							<div class="gw-demo-specs-mini">
								<span>⚙ Auto</span>
								<span>⛽ Petrol</span>
								<span>👤 5 Seats</span>
							</div>
						</div>
						<div class="gw-demo-compact-actions">
							<div class="gw-demo-price-green">
								<strong>$69</strong> <span>/day</span>
							</div>
							<span class="gw-demo-btn-outline-green">Book Now &gt;</span>
						</div>
					</div>

					<!-- Row 3: Mercedes C-Class -->
					<div class="gw-demo-compact-row">
						<div class="gw-demo-compact-media">
							<span class="gw-demo-badge gw-badge-purple">Luxury</span>
							<img src="<?php echo esc_url( $img_base . 'mercedes.jpg' ); ?>" alt="Mercedes C-Class">
						</div>
						<div class="gw-demo-compact-info">
							<h4>Mercedes C-Class</h4>
							<span class="gw-demo-sub">Sedan · 5 Seats</span>
							<div class="gw-demo-rating-mini">⭐ 4.9 (145 reviews)</div>
							<div class="gw-demo-specs-mini">
								<span>⚙ Auto</span>
								<span>⛽ Petrol</span>
								<span>👤 5 Seats</span>
							</div>
						</div>
						<div class="gw-demo-compact-actions">
							<div class="gw-demo-price-green">
								<strong>$79</strong> <span>/day</span>
							</div>
							<span class="gw-demo-btn-outline-green">Book Now &gt;</span>
						</div>
					</div>

				</div>
			</div>
		</div>

	</div>

	<!-- ============================================================== -->
	<!-- SECTION 2: SINGLE RENTAL DESIGNS (3 PRESETS)                   -->
	<!-- ============================================================== -->
	<div id="gw-section-single-designs" class="gw-studio-section" style="display:none;">

		<!-- SINGLE DESIGN 1: Classic Detail -->
		<div class="gw-preset-row <?php echo '1' === $default_single ? 'gw-preset-selected' : ''; ?>" id="gw-single-preset-1">
			<div class="gw-preset-sidebar">
				<div class="gw-num-circle gw-num-blue">1</div>
				<div class="gw-sidebar-info">
					<div class="gw-title-with-pill">
						<h3><?php esc_html_e( 'Single Design 1 — Classic Detail', 'gowheels' ); ?></h3>
						<span class="gw-badge-pill gw-pill-cyan"><?php esc_html_e( 'Gallery & Sidebar', 'gowheels' ); ?></span>
					</div>
					<p class="gw-preset-explanation">
						<?php esc_html_e( 'Left side displays a high-resolution image gallery with clickable thumbnails and complete vehicle overview, while the right side features a sticky pricing card with direct booking form.', 'gowheels' ); ?>
					</p>

					<ul class="gw-checklist">
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Interactive image gallery switcher', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Full technical specifications chips', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'Sticky reservation sidebar card', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-blue"></span> <?php esc_html_e( 'High-conversion responsive layout', 'gowheels' ); ?></li>
					</ul>

					<div class="gw-shortcode-preview-box">
						<span class="gw-sc-lbl"><?php esc_html_e( 'Shortcode (with vehicle ID)', 'gowheels' ); ?></span>
						<div class="gw-code-copy-row">
							<code>[gowheels_single design="1" id="<?php echo esc_attr( $first_vid ); ?>"]</code>
							<button type="button" class="gw-btn-icon gw-copy-btn" data-copy='[gowheels_single design="1" id="<?php echo esc_attr( $first_vid ); ?>"]' title="<?php esc_attr_e( 'Copy Shortcode', 'gowheels' ); ?>">
								<span class="dashicons dashicons-clipboard"></span>
							</button>
						</div>
						<small class="gw-sc-note"><?php esc_html_e( 'On a vehicle single page, id is auto-detected: [gowheels_single design="1"]', 'gowheels' ); ?></small>
					</div>

					<div class="gw-select-action-area">
						<button type="button" class="button gw-select-design-btn <?php echo '1' === $default_single ? 'gw-btn-selected' : 'button-primary'; ?>" data-type="single" data-id="1">
							<?php echo '1' === $default_single ? esc_html__( 'Selected ✓', 'gowheels' ) : esc_html__( 'Choose design', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="gw-preset-preview-panel">
				<div class="gw-single-mockup-wrapper">
					<div class="gw-single-mockup-grid">
						<div class="gw-mock-left-content">
							<div class="gw-mock-main-photo">
								<img src="<?php echo esc_url( $img_base . 'fortuner.jpg' ); ?>" alt="Toyota Fortuner">
								<span class="gw-demo-badge gw-badge-blue">Featured Vehicle</span>
							</div>
							<div class="gw-mock-thumbs">
								<img src="<?php echo esc_url( $img_base . 'fortuner.jpg' ); ?>" class="active" alt="">
								<img src="<?php echo esc_url( $img_base . 'bmw.jpg' ); ?>" alt="">
								<img src="<?php echo esc_url( $img_base . 'mercedes.jpg' ); ?>" alt="">
							</div>
							<div class="gw-mock-spec-pills">
								<span>⚙ Automatic</span>
								<span>⛽ Diesel</span>
								<span>👤 7 Seats</span>
								<span>🧳 3 Luggage</span>
							</div>
							<p class="gw-mock-text">
								Experience unmatched power and reliability with the Toyota Fortuner SUV. Includes 4WD all-wheel drive, spacious leather seating, and modern climate control.
							</p>
						</div>
						<div class="gw-mock-sidebar-form">
							<div class="gw-mock-side-header">
								<span class="gw-mock-side-lbl">Daily Rental</span>
								<div class="gw-mock-side-price">$89 <span>/ day</span></div>
							</div>
							<div class="gw-mock-form-fields">
								<div class="gw-mock-fake-input">📅 Pickup: Tomorrow 10:00 AM</div>
								<div class="gw-mock-fake-input">📅 Return: Next Week 10:00 AM</div>
								<div class="gw-mock-fake-input">📍 Location: Airport Terminal</div>
								<div class="gw-mock-btn-blue">Reserve This Car →</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- SINGLE DESIGN 2: Luxury Detail -->
		<div class="gw-preset-row <?php echo '2' === $default_single ? 'gw-preset-selected' : ''; ?>" id="gw-single-preset-2">
			<div class="gw-preset-sidebar">
				<div class="gw-num-circle gw-num-green">2</div>
				<div class="gw-sidebar-info">
					<div class="gw-title-with-pill">
						<h3><?php esc_html_e( 'Single Design 2 — Luxury Detail', 'gowheels' ); ?></h3>
						<span class="gw-badge-pill gw-pill-green"><?php esc_html_e( 'Hero & VIP Experience', 'gowheels' ); ?></span>
					</div>
					<p class="gw-preset-explanation">
						<?php esc_html_e( 'Large panoramic hero image, premium typography, 4 highlighted feature metric boxes, luxury amenities checklist, and prominent VIP booking panel.', 'gowheels' ); ?>
					</p>

					<ul class="gw-checklist">
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Wide luxury hero banner showcase', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( '4 bold highlighted specification cards', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Structured 3-column amenities checklist', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-green"></span> <?php esc_html_e( 'Full-width VIP booking reservation CTA', 'gowheels' ); ?></li>
					</ul>

					<div class="gw-shortcode-preview-box">
						<span class="gw-sc-lbl"><?php esc_html_e( 'Shortcode (with vehicle ID)', 'gowheels' ); ?></span>
						<div class="gw-code-copy-row">
							<code>[gowheels_single design="2" id="<?php echo esc_attr( $first_vid ); ?>"]</code>
							<button type="button" class="gw-btn-icon gw-copy-btn" data-copy='[gowheels_single design="2" id="<?php echo esc_attr( $first_vid ); ?>"]' title="<?php esc_attr_e( 'Copy Shortcode', 'gowheels' ); ?>">
								<span class="dashicons dashicons-clipboard"></span>
							</button>
						</div>
						<small class="gw-sc-note"><?php esc_html_e( 'On a vehicle single page: [gowheels_single design="2"]', 'gowheels' ); ?></small>
					</div>

					<div class="gw-select-action-area">
						<button type="button" class="button gw-select-design-btn <?php echo '2' === $default_single ? 'gw-btn-selected' : 'button-primary'; ?>" data-type="single" data-id="2">
							<?php echo '2' === $default_single ? esc_html__( 'Selected ✓', 'gowheels' ) : esc_html__( 'Choose design', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="gw-preset-preview-panel">
				<div class="gw-single-mockup-wrapper">
					<div class="gw-luxury-mockup-view">
						<div class="gw-luxury-hero-banner">
							<img src="<?php echo esc_url( $img_base . 'bmw.jpg' ); ?>" alt="BMW 3 Series">
							<div class="gw-luxury-hero-overlay">
								<span class="gw-badge-pill gw-pill-green">Executive Class</span>
								<h3>BMW 3 Series Sedan</h3>
								<div class="gw-luxury-rate">$69 <span>/ day</span></div>
							</div>
						</div>
						<div class="gw-luxury-4matrix">
							<div class="gw-matrix-box">
								<strong>⚙ Automatic</strong>
								<small>Transmission</small>
							</div>
							<div class="gw-matrix-box">
								<strong>⛽ Petrol Turbo</strong>
								<small>Engine & Fuel</small>
							</div>
							<div class="gw-matrix-box">
								<strong>👤 5 Passengers</strong>
								<small>Capacity</small>
							</div>
							<div class="gw-matrix-box">
								<strong>🧳 3 Suitcases</strong>
								<small>Luggage</small>
							</div>
						</div>
						<div class="gw-luxury-cta-banner">
							<div>
								<strong>Reserve with Best Rate Guarantee</strong>
								<small>Instant confirmation · Zero cancellation fees up to 24h before pickup</small>
							</div>
							<span class="gw-demo-btn-green">Book Vehicle Now</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- SINGLE DESIGN 3: Split Layout -->
		<div class="gw-preset-row <?php echo '3' === $default_single ? 'gw-preset-selected' : ''; ?>" id="gw-single-preset-3">
			<div class="gw-preset-sidebar">
				<div class="gw-num-circle gw-num-purple">3</div>
				<div class="gw-sidebar-info">
					<div class="gw-title-with-pill">
						<h3><?php esc_html_e( 'Single Design 3 — Split Layout', 'gowheels' ); ?></h3>
						<span class="gw-badge-pill gw-pill-purple"><?php esc_html_e( 'Interactive Tabs & Compact', 'gowheels' ); ?></span>
					</div>
					<p class="gw-preset-explanation">
						<?php esc_html_e( 'Vehicle media and overview alongside interactive tabbed panels (Overview, Technical Specs, Amenities, Rental Policies) and a sticky bottom booking bar.', 'gowheels' ); ?>
					</p>

					<ul class="gw-checklist">
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( '4 organized interactive tabs', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Technical specifications table & checklist', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Transparent rental policies & insurance notes', 'gowheels' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt gw-check-purple"></span> <?php esc_html_e( 'Floating bottom reservation bar', 'gowheels' ); ?></li>
					</ul>

					<div class="gw-shortcode-preview-box">
						<span class="gw-sc-lbl"><?php esc_html_e( 'Shortcode (with vehicle ID)', 'gowheels' ); ?></span>
						<div class="gw-code-copy-row">
							<code>[gowheels_single design="3" id="<?php echo esc_attr( $first_vid ); ?>"]</code>
							<button type="button" class="gw-btn-icon gw-copy-btn" data-copy='[gowheels_single design="3" id="<?php echo esc_attr( $first_vid ); ?>"]' title="<?php esc_attr_e( 'Copy Shortcode', 'gowheels' ); ?>">
								<span class="dashicons dashicons-clipboard"></span>
							</button>
						</div>
						<small class="gw-sc-note"><?php esc_html_e( 'On a vehicle single page: [gowheels_single design="3"]', 'gowheels' ); ?></small>
					</div>

					<div class="gw-select-action-area">
						<button type="button" class="button gw-select-design-btn <?php echo '3' === $default_single ? 'gw-btn-selected' : 'button-primary'; ?>" data-type="single" data-id="3">
							<?php echo '3' === $default_single ? esc_html__( 'Selected ✓', 'gowheels' ) : esc_html__( 'Choose design', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="gw-preset-preview-panel">
				<div class="gw-single-mockup-wrapper">
					<div class="gw-split-mockup-view">
						<div class="gw-split-top-bar">
							<div>
								<h4>Mercedes C-Class Luxury</h4>
								<span class="gw-demo-sub">Sedan · 5 Seats · Unlimited Miles</span>
							</div>
							<div class="gw-demo-price-blue">$79 <span>/ day</span></div>
						</div>
						<div class="gw-mock-nav-tabs">
							<span class="active">Overview</span>
							<span>Technical Specs</span>
							<span>Included Features</span>
							<span>Rental Terms</span>
						</div>
						<div class="gw-mock-tab-panel">
							<div class="gw-mock-spec-table">
								<div><strong>Engine</strong><span>2.0L Turbo 4-Cylinder</span></div>
								<div><strong>Gearbox</strong><span>9-Speed G-Tronic Auto</span></div>
								<div><strong>Fuel Economy</strong><span>34 MPG Highway</span></div>
								<div><strong>Security Deposit</strong><span>$200 (Refundable)</span></div>
							</div>
						</div>
						<div class="gw-mock-bottom-bar-cta">
							<span>Starting at $79/day with Comprehensive Coverage</span>
							<span class="gw-demo-btn-blue">Book This Vehicle Now</span>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<!-- Toast notification for AJAX saving -->
	<div id="gw-toast-notice" class="gw-toast-notice" style="display:none;"></div>

</div>
