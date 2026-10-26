<?php
defined( 'ABSPATH' ) || exit;

/**
 * Dashboard & Shortcodes Tabbed Design View
 *
 * @var array  $counts
 * @var float  $revenue
 * @var array  $pending
 * @var array  $upcoming
 * @var string $active_tab
 * @var array  $vehicles
 */
?>
<div class="wrap gw-admin-wrap">

	<!-- Header Banner -->
	<div class="gw-dashboard-header">
		<div class="gw-dashboard-title-area">
			<div class="gw-brand-badge">
				<span class="dashicons dashicons-car"></span>
				<strong>GoWheels</strong>
				<span class="gw-version-pill">v<?php echo esc_html( GOWHEELS_VERSION ); ?></span>
			</div>
			<h1><?php esc_html_e( 'Car Rental Dashboard & Design Presets', 'gowheels' ); ?></h1>
			<p class="gw-dashboard-subtitle">
				<?php esc_html_e( 'Choose from 3 vehicle card designs, an interactive tabbed fleet layout, and 3 single car rental templates with ready-to-use shortcodes.', 'gowheels' ); ?>
			</p>
		</div>
		<div class="gw-header-actions">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . GoWheels_Vehicle_CPT::TYPE ) ); ?>" class="button button-primary">
				<span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Add Vehicle', 'gowheels' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=gowheels-settings' ) ); ?>" class="button">
				<span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Settings', 'gowheels' ); ?>
			</a>
		</div>
	</div>

	<!-- Navigation Tabs -->
	<nav class="gw-nav-tabs" role="tablist">
		<button type="button" class="gw-tab-btn <?php echo 'overview' === $active_tab ? 'active' : ''; ?>" data-tab="gw-tab-overview" role="tab">
			<span class="dashicons dashicons-chart-bar"></span>
			<?php esc_html_e( 'Overview & Bookings', 'gowheels' ); ?>
		</button>
		<button type="button" class="gw-tab-btn <?php echo 'cards' === $active_tab ? 'active' : ''; ?>" data-tab="gw-tab-cards" role="tab">
			<span class="dashicons dashicons-grid-view"></span>
			<?php esc_html_e( '3 Card Designs', 'gowheels' ); ?>
			<span class="gw-tab-pill">3 Presets</span>
		</button>
		<button type="button" class="gw-tab-btn <?php echo 'fleet-tabs' === $active_tab ? 'active' : ''; ?>" data-tab="gw-tab-fleet-tabs" role="tab">
			<span class="dashicons dashicons-index-card"></span>
			<?php esc_html_e( 'Fleet Tabs Design', 'gowheels' ); ?>
			<span class="gw-tab-pill gw-pill-accent">Interactive</span>
		</button>
		<button type="button" class="gw-tab-btn <?php echo 'single-car' === $active_tab ? 'active' : ''; ?>" data-tab="gw-tab-single-car" role="tab">
			<span class="dashicons dashicons-format-gallery"></span>
			<?php esc_html_e( '3 Single Car Designs', 'gowheels' ); ?>
			<span class="gw-tab-pill">3 Templates</span>
		</button>
		<button type="button" class="gw-tab-btn <?php echo 'shortcodes' === $active_tab ? 'active' : ''; ?>" data-tab="gw-tab-shortcodes" role="tab">
			<span class="dashicons dashicons-shortcode"></span>
			<?php esc_html_e( 'Shortcode Builder & Reference', 'gowheels' ); ?>
		</button>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=gowheels-design' ) ); ?>" class="gw-tab-btn" style="text-decoration:none; margin-left: auto;">
			<span class="dashicons dashicons-art" style="color:#2563eb;"></span>
			<strong><?php esc_html_e( 'Open Design Studio', 'gowheels' ); ?></strong>
			<span class="gw-tab-pill gw-pill-accent"><?php esc_html_e( 'New', 'gowheels' ); ?></span>
		</a>
	</nav>

	<!-- ========================================== -->
	<!-- TAB 1: OVERVIEW & BOOKINGS                 -->
	<!-- ========================================== -->
	<div id="gw-tab-overview" class="gw-tab-pane <?php echo 'overview' === $active_tab ? 'active' : ''; ?>" role="tabpanel">
		<div class="gw-stats">
			<div class="gw-stat">
				<span class="gw-stat-num"><?php echo esc_html( isset( $counts['pending_review'] ) ? (int) $counts['pending_review']->c : 0 ); ?></span>
				<span class="gw-stat-lbl"><?php esc_html_e( 'Pending review', 'gowheels' ); ?></span>
			</div>
			<div class="gw-stat">
				<span class="gw-stat-num"><?php echo esc_html( isset( $counts['approved'] ) ? (int) $counts['approved']->c : 0 ); ?></span>
				<span class="gw-stat-lbl"><?php esc_html_e( 'Approved', 'gowheels' ); ?></span>
			</div>
			<div class="gw-stat">
				<span class="gw-stat-num"><?php echo esc_html( isset( $counts['in_progress'] ) ? (int) $counts['in_progress']->c : 0 ); ?></span>
				<span class="gw-stat-lbl"><?php esc_html_e( 'In progress', 'gowheels' ); ?></span>
			</div>
			<div class="gw-stat">
				<span class="gw-stat-num"><?php echo esc_html( isset( $counts['completed'] ) ? (int) $counts['completed']->c : 0 ); ?></span>
				<span class="gw-stat-lbl"><?php esc_html_e( 'Completed', 'gowheels' ); ?></span>
			</div>
			<div class="gw-stat">
				<span class="gw-stat-num"><?php echo esc_html( count( $vehicles ) ); ?></span>
				<span class="gw-stat-lbl"><?php esc_html_e( 'Vehicles', 'gowheels' ); ?></span>
			</div>
			<div class="gw-stat gw-stat-revenue">
				<span class="gw-stat-num"><?php echo esc_html( GoWheels_Util::money( $revenue ) ); ?></span>
				<span class="gw-stat-lbl"><?php esc_html_e( 'Net revenue', 'gowheels' ); ?></span>
			</div>
		</div>

		<!-- Quick Callout for Presets -->
		<div class="gw-callout-banner">
			<div class="gw-callout-icon"><span class="dashicons dashicons-art"></span></div>
			<div class="gw-callout-content">
				<h3><?php esc_html_e( 'GoWheels Design Studio: 3 Preset Card Designs & 3 Single Rental Designs', 'gowheels' ); ?></h3>
				<p><?php esc_html_e( 'Preview all 6 presets with side-by-side automotive mockups, select your default design with 1-click, and copy ready-to-use shortcodes [gowheels_cars design="1|2|3"] and [gowheels_single design="1|2|3"].', 'gowheels' ); ?></p>
			</div>
			<div class="gw-callout-btn">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=gowheels-design' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Open Design Studio →', 'gowheels' ); ?>
				</a>
			</div>
		</div>

		<!-- Pending Reviews Table -->
		<div class="gw-section-card">
			<h2><?php esc_html_e( 'Waiting for approval', 'gowheels' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Ref', 'gowheels' ); ?></th>
						<th><?php esc_html_e( 'Customer', 'gowheels' ); ?></th>
						<th><?php esc_html_e( 'Vehicle', 'gowheels' ); ?></th>
						<th><?php esc_html_e( 'Pickup Date', 'gowheels' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $pending ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'No bookings waiting for approval.', 'gowheels' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $pending as $b ) : ?>
							<tr>
								<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=gowheels-bookings&view=' . (int) $b->id ) ); ?>"><strong><?php echo esc_html( $b->booking_ref ); ?></strong></a></td>
								<td><?php echo esc_html( $b->customer_name ); ?></td>
								<td><?php echo esc_html( get_the_title( $b->vehicle_id ) ); ?></td>
								<td><?php echo esc_html( GoWheels_Util::to_local( $b->pickup_at_utc ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<!-- Upcoming Pickups Table -->
		<div class="gw-section-card">
			<h2><?php esc_html_e( 'Pickups in the next 7 days', 'gowheels' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Ref', 'gowheels' ); ?></th>
						<th><?php esc_html_e( 'Customer', 'gowheels' ); ?></th>
						<th><?php esc_html_e( 'Vehicle', 'gowheels' ); ?></th>
						<th><?php esc_html_e( 'Pickup Date', 'gowheels' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $upcoming ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'No upcoming pickups in the next 7 days.', 'gowheels' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $upcoming as $b ) : ?>
							<tr>
								<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=gowheels-bookings&view=' . (int) $b->id ) ); ?>"><strong><?php echo esc_html( $b->booking_ref ); ?></strong></a></td>
								<td><?php echo esc_html( $b->customer_name ); ?></td>
								<td><?php echo esc_html( get_the_title( $b->vehicle_id ) ); ?></td>
								<td><?php echo esc_html( GoWheels_Util::to_local( $b->pickup_at_utc ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- ========================================== -->
	<!-- TAB 2: 3 CARD DESIGNS PRESETS              -->
	<!-- ========================================== -->
	<div id="gw-tab-cards" class="gw-tab-pane <?php echo 'cards' === $active_tab ? 'active' : ''; ?>" role="tabpanel">
		<div class="gw-tab-intro">
			<h2><?php esc_html_e( '3 Vehicle Card Design Presets', 'gowheels' ); ?></h2>
			<p><?php esc_html_e( 'Select from 3 distinct card styles for your vehicle grid or catalog. Click the copy button to copy the shortcode and paste it into any WordPress page, post, Elementor, or Gutenberg block.', 'gowheels' ); ?></p>
		</div>

		<div class="gw-presets-grid">

			<!-- PRESET 1: Classic Card -->
			<div class="gw-preset-card">
				<div class="gw-preset-header">
					<div class="gw-preset-badge gw-badge-classic"><?php esc_html_e( 'Preset 1: Classic', 'gowheels' ); ?></div>
					<h3><?php esc_html_e( 'Classic Grid Card', 'gowheels' ); ?></h3>
					<p class="gw-preset-desc"><?php esc_html_e( 'Clean, balanced card with framed vehicle media, inline specs bar, and dual action buttons.', 'gowheels' ); ?></p>
				</div>

				<!-- Mockup Preview -->
				<div class="gw-mockup gw-mockup-classic">
					<div class="gw-mock-img-box">
						<span class="dashicons dashicons-car gw-mock-car-icon"></span>
						<div class="gw-mock-pill">$85 / day</div>
					</div>
					<div class="gw-mock-body">
						<div class="gw-mock-title">Executive Luxury Sedan</div>
						<div class="gw-mock-specs">
							<span>👤 5 Seats</span>
							<span>⚙ Auto</span>
							<span>⛽ Hybrid</span>
							<span>🧳 2 Bags</span>
						</div>
						<div class="gw-mock-btns">
							<span class="gw-mock-btn-outline">View Details</span>
							<span class="gw-mock-btn-solid">Book Now</span>
						</div>
					</div>
				</div>

				<!-- Code & Copy Area -->
				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Primary Shortcode:', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_cars card_style="classic"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="classic"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( '3 Columns Preset (Recommended):', 'gowheels' ); ?></label>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_cars card_style="classic" columns="3" limit="6"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="classic" columns="3" limit="6"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<ul class="gw-feature-list">
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Clean bordered card design with smooth hover', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Dual action: View Details + Direct Booking', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Responsive grid support (1, 2, 3, or 4 columns)', 'gowheels' ); ?></li>
				</ul>
			</div>

			<!-- PRESET 2: Modern Badge Card -->
			<div class="gw-preset-card gw-card-featured">
				<div class="gw-preset-header">
					<div class="gw-preset-badge gw-badge-modern"><?php esc_html_e( 'Preset 2: Modern (Recommended)', 'gowheels' ); ?></div>
					<h3><?php esc_html_e( 'Modern Badge Card', 'gowheels' ); ?></h3>
					<p class="gw-preset-desc"><?php esc_html_e( 'High-conversion design with floating badges, SVG spec pills, feature tags, and zoom effects.', 'gowheels' ); ?></p>
				</div>

				<!-- Mockup Preview -->
				<div class="gw-mockup gw-mockup-modern">
					<div class="gw-mock-img-box gw-mock-gradient">
						<span class="gw-mock-float-badge">Featured</span>
						<div class="gw-mock-pill gw-mock-pill-emerald">$95 / day</div>
						<span class="dashicons dashicons-car gw-mock-car-icon gw-text-white"></span>
					</div>
					<div class="gw-mock-body">
						<div class="gw-mock-title">Sport Edition Coupe</div>
						<div class="gw-mock-chips">
							<span class="gw-chip">⚙ Automatic</span>
							<span class="gw-chip">⚡ Electric</span>
							<span class="gw-chip">👤 4 Seats</span>
						</div>
						<div class="gw-mock-features">
							<span>• GPS Nav</span>
							<span>• Bluetooth</span>
							<span>• Heated Seats</span>
						</div>
						<div class="gw-mock-btn-cta">Rent Vehicle →</div>
					</div>
				</div>

				<!-- Code & Copy Area -->
				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Primary Shortcode:', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_cars card_style="modern"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="modern"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( '3 Columns Preset (Recommended):', 'gowheels' ); ?></label>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_cars card_style="modern" columns="3" limit="6"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="modern" columns="3" limit="6"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<ul class="gw-feature-list">
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Floating price pill and status badges', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'SVG spec chips and highlighted amenity tags', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Vibrant high-contrast CTA button with hover micro-animations', 'gowheels' ); ?></li>
				</ul>
			</div>

			<!-- PRESET 3: Horizontal Row Card -->
			<div class="gw-preset-card">
				<div class="gw-preset-header">
					<div class="gw-preset-badge gw-badge-horizontal"><?php esc_html_e( 'Preset 3: Horizontal Row', 'gowheels' ); ?></div>
					<h3><?php esc_html_e( 'Horizontal Row Card', 'gowheels' ); ?></h3>
					<p class="gw-preset-desc"><?php esc_html_e( 'Wide banner layout with media on the left, specifications in the middle, and actions on the right.', 'gowheels' ); ?></p>
				</div>

				<!-- Mockup Preview -->
				<div class="gw-mockup gw-mockup-horizontal">
					<div class="gw-mock-h-row">
						<div class="gw-mock-h-img">
							<span class="dashicons dashicons-car"></span>
						</div>
						<div class="gw-mock-h-info">
							<div class="gw-mock-title">Full-Size Family SUV</div>
							<div class="gw-mock-chips">
								<span class="gw-chip">👤 7 Seats</span>
								<span class="gw-chip">⚙ Automatic</span>
								<span class="gw-chip">🧳 4 Bags</span>
							</div>
							<div class="gw-mock-meta-text">Available with Unlimited Mileage</div>
						</div>
						<div class="gw-mock-h-action">
							<div class="gw-mock-h-price">$120 <span>/ day</span></div>
							<div class="gw-mock-btn-solid">Reserve</div>
						</div>
					</div>
				</div>

				<!-- Code & Copy Area -->
				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Primary Shortcode:', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_cars card_style="horizontal"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="horizontal"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( 'Horizontal Listing Preset (5 per page):', 'gowheels' ); ?></label>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_cars card_style="horizontal" limit="5"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="horizontal" limit="5"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<ul class="gw-feature-list">
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Optimized for directory listings and search results', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Dedicated media, specifications, and action columns', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Collapses into clean vertical cards on mobile devices', 'gowheels' ); ?></li>
				</ul>
			</div>

		</div>
	</div>

	<!-- ========================================== -->
	<!-- TAB 3: FLEET TABS DESIGN PRESET            -->
	<!-- ========================================== -->
	<div id="gw-tab-fleet-tabs" class="gw-tab-pane <?php echo 'fleet-tabs' === $active_tab ? 'active' : ''; ?>" role="tabpanel">
		<div class="gw-tab-intro">
			<h2><?php esc_html_e( 'Interactive Tabbed Fleet Design Preset', 'gowheels' ); ?></h2>
			<p><?php esc_html_e( 'Display your vehicle fleet with interactive filter buttons (All, Sedan, SUV, Luxury, Automatic, etc.) and real-time live count badges without page reloading.', 'gowheels' ); ?></p>
		</div>

		<div class="gw-fleet-tabs-showcase">
			<div class="gw-tabs-preview-box">
				<!-- Mockup Interactive Tabs Bar -->
				<div class="gw-mockup-tab-bar">
					<span class="gw-mock-tab-btn active">All Fleet <span class="gw-count-badge">8</span></span>
					<span class="gw-mock-tab-btn">Automatic <span class="gw-count-badge">5</span></span>
					<span class="gw-mock-tab-btn">Manual <span class="gw-count-badge">3</span></span>
					<span class="gw-mock-tab-btn">Electric / Hybrid <span class="gw-count-badge">2</span></span>
				</div>

				<div class="gw-mockup-tab-grid">
					<div class="gw-mock-card-mini">
						<div class="gw-mock-img-placeholder"><span class="dashicons dashicons-car"></span></div>
						<strong>Sedan Luxury</strong>
						<small>⚙ Auto · 👤 5 · $80/day</small>
					</div>
					<div class="gw-mock-card-mini">
						<div class="gw-mock-img-placeholder"><span class="dashicons dashicons-car"></span></div>
						<strong>SUV All-Wheel</strong>
						<small>⚙ Auto · 👤 7 · $110/day</small>
					</div>
					<div class="gw-mock-card-mini">
						<div class="gw-mock-img-placeholder"><span class="dashicons dashicons-car"></span></div>
						<strong>Compact City Car</strong>
						<small>⚙ Manual · 👤 4 · $55/day</small>
					</div>
				</div>
			</div>

			<div class="gw-fleet-tabs-details">
				<h3><?php esc_html_e( 'Fleet Tabs Shortcodes & Options', 'gowheels' ); ?></h3>

				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Method 1: Using layout="tabs" on [gowheels_cars]:', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_cars layout="tabs" card_style="modern" limit="9"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars layout="tabs" card_style="modern" limit="9"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( 'Method 2: Dedicated Shortcut Shortcode [gowheels_cars_tabs]:', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_cars_tabs card_style="modern"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars_tabs card_style="modern"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( 'With Classic Card Style:', 'gowheels' ); ?></label>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_cars layout="tabs" card_style="classic" limit="12"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars layout="tabs" card_style="classic" limit="12"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<div class="gw-attributes-summary">
					<h4><?php esc_html_e( 'Key Features & Capabilities:', 'gowheels' ); ?></h4>
					<ul>
						<li><strong><?php esc_html_e( 'Zero Page Reloads:', 'gowheels' ); ?></strong> <?php esc_html_e( 'Instant vanilla JavaScript client-side tab switching.', 'gowheels' ); ?></li>
						<li><strong><?php esc_html_e( 'Live Count Badges:', 'gowheels' ); ?></strong> <?php esc_html_e( 'Automatically displays the number of cars available in each tab category.', 'gowheels' ); ?></li>
						<li><strong><?php esc_html_e( 'Universal Style Pairing:', 'gowheels' ); ?></strong> <?php esc_html_e( 'Works seamlessly with all 3 card styles: modern, classic, or horizontal.', 'gowheels' ); ?></li>
						<li><strong><?php esc_html_e( 'Mobile Swipe Navigation:', 'gowheels' ); ?></strong> <?php esc_html_e( 'Smooth horizontal scrolling tabs bar on touch devices.', 'gowheels' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- ========================================== -->
	<!-- TAB 4: 3 SINGLE CAR RENTAL PRESETS         -->
	<!-- ========================================== -->
	<div id="gw-tab-single-car" class="gw-tab-pane <?php echo 'single-car' === $active_tab ? 'active' : ''; ?>" role="tabpanel">
		<div class="gw-tab-intro">
			<h2><?php esc_html_e( '3 Single Car Rental Design Templates', 'gowheels' ); ?></h2>
			<p><?php esc_html_e( 'Choose from 3 distinct templates for individual vehicle display pages. Use the shortcode with or without an id="ID" attribute. When used on a vehicle single post page, the ID is detected automatically.', 'gowheels' ); ?></p>
		</div>

		<div class="gw-presets-grid">

			<!-- TEMPLATE 1: Modern Split Showcase -->
			<div class="gw-preset-card gw-card-featured">
				<div class="gw-preset-header">
					<div class="gw-preset-badge gw-badge-modern"><?php esc_html_e( 'Template 1: Modern Split', 'gowheels' ); ?></div>
					<h3><?php esc_html_e( 'Modern Split Showcase', 'gowheels' ); ?></h3>
					<p class="gw-preset-desc"><?php esc_html_e( 'Dynamic 2-column showcase with interactive thumbnail preview gallery and sticky booking sidebar card.', 'gowheels' ); ?></p>
				</div>

				<!-- Mockup Preview -->
				<div class="gw-mockup gw-mockup-single-modern">
					<div class="gw-mock-single-grid">
						<div class="gw-mock-left-col">
							<div class="gw-mock-hero-img">
								<span class="dashicons dashicons-car"></span>
								<div class="gw-mock-pill">$95 / day</div>
							</div>
							<div class="gw-mock-thumb-row">
								<span class="gw-thumb-dot active"></span>
								<span class="gw-thumb-dot"></span>
								<span class="gw-thumb-dot"></span>
							</div>
							<div class="gw-mock-spec-chips">
								<span>⚙ Auto</span>
								<span>⛽ Hybrid</span>
								<span>👤 5 Seats</span>
							</div>
						</div>
						<div class="gw-mock-right-col">
							<div class="gw-mock-sidebar-card">
								<div class="gw-mock-side-title">Book This Car</div>
								<div class="gw-mock-side-price">$95 <span>/ day</span></div>
								<div class="gw-mock-input-mini">Pickup Date</div>
								<div class="gw-mock-input-mini">Return Date</div>
								<div class="gw-mock-btn-solid">Reserve Now</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Code & Copy Area -->
				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Auto-detect (Single Vehicle Post):', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_car template="modern"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car template="modern"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( 'Specific Vehicle ID (Landing Pages):', 'gowheels' ); ?></label>
					<?php $first_vid = ! empty( $vehicles ) ? $vehicles[0]['id'] : '215'; ?>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_car id="<?php echo esc_attr( $first_vid ); ?>" template="modern"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car id="<?php echo esc_attr( $first_vid ); ?>" template="modern"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<ul class="gw-feature-list">
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Dynamic image swap on thumbnail click', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Sticky reservation sidebar stays in view', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Visual spec cards and included features pills', 'gowheels' ); ?></li>
				</ul>
			</div>

			<!-- TEMPLATE 2: Tabbed Executive View -->
			<div class="gw-preset-card">
				<div class="gw-preset-header">
					<div class="gw-preset-badge gw-badge-classic"><?php esc_html_e( 'Template 2: Executive Tabs', 'gowheels' ); ?></div>
					<h3><?php esc_html_e( 'Tabbed Executive View', 'gowheels' ); ?></h3>
					<p class="gw-preset-desc"><?php esc_html_e( 'Structured executive layout with 4 organized tab panels and sticky bottom booking bar.', 'gowheels' ); ?></p>
				</div>

				<!-- Mockup Preview -->
				<div class="gw-mockup gw-mockup-single-tabs">
					<div class="gw-mock-exec-hero">
						<div class="gw-mock-title">Executive Grand Tourer</div>
						<span class="gw-mock-pill">$135 / day</span>
					</div>
					<div class="gw-mock-inner-tabs">
						<span class="active">Overview</span>
						<span>Technical Specs</span>
						<span>Features</span>
						<span>Rental Terms</span>
					</div>
					<div class="gw-mock-tab-body">
						<div class="gw-mock-spec-matrix">
							<span>Engine: V6 Twin-Turbo</span>
							<span>Gearbox: 8-Speed Auto</span>
							<span>Luggage: 3 Bags</span>
							<span>Mileage: Unlimited</span>
						</div>
					</div>
					<div class="gw-mock-bottom-bar">
						<span>Ready to book?</span>
						<span class="gw-mock-btn-solid">Reserve ($135/day)</span>
					</div>
				</div>

				<!-- Code & Copy Area -->
				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Auto-detect (Single Vehicle Post):', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_car template="tabs"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car template="tabs"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( 'Specific Vehicle ID (Landing Pages):', 'gowheels' ); ?></label>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_car id="<?php echo esc_attr( $first_vid ); ?>" template="tabs"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car id="<?php echo esc_attr( $first_vid ); ?>" template="tabs"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<ul class="gw-feature-list">
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( '4 tabs: Overview, Specs, Features, and Policies', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Technical specifications table & checklist', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Convenient floating bottom reservation bar', 'gowheels' ); ?></li>
				</ul>
			</div>

			<!-- TEMPLATE 3: Luxury Classic Grid -->
			<div class="gw-preset-card">
				<div class="gw-preset-header">
					<div class="gw-preset-badge gw-badge-horizontal"><?php esc_html_e( 'Template 3: Luxury Classic', 'gowheels' ); ?></div>
					<h3><?php esc_html_e( 'Luxury Classic Grid', 'gowheels' ); ?></h3>
					<p class="gw-preset-desc"><?php esc_html_e( 'Centered luxury layout with 4 bold key metrics cards, 3-column amenities checklist, and VIP CTA.', 'gowheels' ); ?></p>
				</div>

				<!-- Mockup Preview -->
				<div class="gw-mockup gw-mockup-single-classic">
					<div class="gw-mock-hero-img">
						<span class="dashicons dashicons-car"></span>
					</div>
					<div class="gw-mock-4cards">
						<div class="gw-mock-metric"><strong>Auto</strong><small>Gearbox</small></div>
						<div class="gw-mock-metric"><strong>Petrol</strong><small>Fuel</small></div>
						<div class="gw-mock-metric"><strong>5</strong><small>Seats</small></div>
						<div class="gw-mock-metric"><strong>3</strong><small>Luggage</small></div>
					</div>
					<div class="gw-mock-classic-cta">
						<span>Best Rate Guarantee — From $110/day</span>
						<span class="gw-mock-btn-solid">Reserve Vehicle</span>
					</div>
				</div>

				<!-- Code & Copy Area -->
				<div class="gw-preset-code-area">
					<label><?php esc_html_e( 'Auto-detect (Single Vehicle Post):', 'gowheels' ); ?></label>
					<div class="gw-code-box">
						<code>[gowheels_car template="classic"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car template="classic"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>

					<label class="gw-sub-lbl"><?php esc_html_e( 'Specific Vehicle ID (Landing Pages):', 'gowheels' ); ?></label>
					<div class="gw-code-box gw-code-compact">
						<code>[gowheels_car id="<?php echo esc_attr( $first_vid ); ?>" template="classic"]</code>
						<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car id="<?php echo esc_attr( $first_vid ); ?>" template="classic"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
						</button>
					</div>
				</div>

				<ul class="gw-feature-list">
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( '4-card bold key metrics showcase matrix', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( '3-column structured amenities list', 'gowheels' ); ?></li>
					<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Full-width VIP booking reservation card', 'gowheels' ); ?></li>
				</ul>
			</div>

		</div>
	</div>

	<!-- ========================================== -->
	<!-- TAB 5: SHORTCODE BUILDER & CHEAT-SHEET     -->
	<!-- ========================================== -->
	<div id="gw-tab-shortcodes" class="gw-tab-pane <?php echo 'shortcodes' === $active_tab ? 'active' : ''; ?>" role="tabpanel">
		<div class="gw-tab-intro">
			<h2><?php esc_html_e( 'Interactive Shortcode Builder', 'gowheels' ); ?></h2>
			<p><?php esc_html_e( 'Customize shortcode parameters dynamically and copy the generated shortcode with one click.', 'gowheels' ); ?></p>
		</div>

		<!-- Interactive Generator Form -->
		<div class="gw-generator-card">
			<form id="gw-generator-form" class="gw-gen-form" onsubmit="return false;">
				<div class="gw-gen-grid">

					<!-- Shortcode Type -->
					<div class="gw-gen-field-group">
						<label for="gw-gen-type"><?php esc_html_e( 'Shortcode Type:', 'gowheels' ); ?></label>
						<select id="gw-gen-type" class="gw-select-input">
							<option value="cars_grid" selected><?php esc_html_e( 'Fleet Vehicles Grid [gowheels_cars]', 'gowheels' ); ?></option>
							<option value="cars_tabs"><?php esc_html_e( 'Fleet with Interactive Tabs [gowheels_cars layout="tabs"]', 'gowheels' ); ?></option>
							<option value="single_car"><?php esc_html_e( 'Single Vehicle Details [gowheels_car]', 'gowheels' ); ?></option>
							<option value="search"><?php esc_html_e( 'Fleet Search Filter Bar [gowheels_search]', 'gowheels' ); ?></option>
							<option value="booking"><?php esc_html_e( 'Direct Booking Form [gowheels_booking]', 'gowheels' ); ?></option>
							<option value="status"><?php esc_html_e( 'Booking Status Lookup [gowheels_booking_status]', 'gowheels' ); ?></option>
						</select>
					</div>

					<!-- Card Style (Grid) -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-grid-field">
						<label for="gw-gen-card-style"><?php esc_html_e( 'Card Design Style:', 'gowheels' ); ?></label>
						<select id="gw-gen-card-style" class="gw-select-input">
							<option value="modern" selected><?php esc_html_e( 'Modern Badge Card (modern)', 'gowheels' ); ?></option>
							<option value="classic"><?php esc_html_e( 'Classic Grid Card (classic)', 'gowheels' ); ?></option>
							<option value="horizontal"><?php esc_html_e( 'Horizontal Row Card (horizontal)', 'gowheels' ); ?></option>
						</select>
					</div>

					<!-- Columns (Grid) -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-grid-field">
						<label for="gw-gen-cols"><?php esc_html_e( 'Columns:', 'gowheels' ); ?></label>
						<select id="gw-gen-cols" class="gw-select-input">
							<option value="3" selected>3 Columns</option>
							<option value="2">2 Columns</option>
							<option value="4">4 Columns</option>
							<option value="1">1 Column (List)</option>
						</select>
					</div>

					<!-- Limit (Grid) -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-grid-field">
						<label for="gw-gen-limit"><?php esc_html_e( 'Number of Vehicles (Limit):', 'gowheels' ); ?></label>
						<input type="number" id="gw-gen-limit" value="6" min="1" max="50" class="gw-num-input" />
					</div>

					<!-- Card Style (Tabs) -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-tabs-field" style="display:none;">
						<label for="gw-gen-tab-card-style"><?php esc_html_e( 'Card Design Style:', 'gowheels' ); ?></label>
						<select id="gw-gen-tab-card-style" class="gw-select-input">
							<option value="modern" selected><?php esc_html_e( 'Modern Badge Card (modern)', 'gowheels' ); ?></option>
							<option value="classic"><?php esc_html_e( 'Classic Grid Card (classic)', 'gowheels' ); ?></option>
							<option value="horizontal"><?php esc_html_e( 'Horizontal Row Card (horizontal)', 'gowheels' ); ?></option>
						</select>
					</div>

					<!-- Limit (Tabs) -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-tabs-field" style="display:none;">
						<label for="gw-gen-tab-limit"><?php esc_html_e( 'Number of Vehicles:', 'gowheels' ); ?></label>
						<input type="number" id="gw-gen-tab-limit" value="9" min="1" max="50" class="gw-num-input" />
					</div>

					<!-- Single Car Template -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-single-field" style="display:none;">
						<label for="gw-gen-template"><?php esc_html_e( 'Single Car Template:', 'gowheels' ); ?></label>
						<select id="gw-gen-template" class="gw-select-input">
							<option value="modern" selected><?php esc_html_e( 'Template 1: Modern Split Showcase (modern)', 'gowheels' ); ?></option>
							<option value="tabs"><?php esc_html_e( 'Template 2: Tabbed Executive View (tabs)', 'gowheels' ); ?></option>
							<option value="classic"><?php esc_html_e( 'Template 3: Luxury Classic Grid (classic)', 'gowheels' ); ?></option>
						</select>
					</div>

					<!-- Single Car Vehicle Selection -->
					<div class="gw-gen-field-group gw-gen-field gw-gen-single-field gw-gen-vehicle-field" style="display:none;">
						<label for="gw-gen-vehicle-id"><?php esc_html_e( 'Select Vehicle (Optional):', 'gowheels' ); ?></label>
						<select id="gw-gen-vehicle-id" class="gw-select-input">
							<option value=""><?php esc_html_e( '— Auto-detect (for single vehicle posts) —', 'gowheels' ); ?></option>
							<?php foreach ( $vehicles as $v ) : ?>
								<option value="<?php echo esc_attr( $v['id'] ); ?>">
									<?php echo esc_html( $v['title'] . ' (ID: ' . $v['id'] . ')' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

				</div>

				<!-- Live Output Box -->
				<div class="gw-gen-output-box">
					<div class="gw-output-label">
						<strong><?php esc_html_e( 'Generated Shortcode:', 'gowheels' ); ?></strong>
						<span class="gw-output-hint"><?php esc_html_e( 'Ready to paste into your WordPress page, post, or widget.', 'gowheels' ); ?></span>
					</div>
					<div class="gw-output-bar">
						<code id="gw-generated-code">[gowheels_cars card_style="modern" columns="3" limit="6"]</code>
						<button type="button" id="gw-gen-copy-btn" class="button button-primary gw-copy-btn" data-copy='[gowheels_cars card_style="modern" columns="3" limit="6"]'>
							<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy Generated Code', 'gowheels' ); ?>
						</button>
					</div>
				</div>
			</form>
		</div>

		<!-- Complete Shortcodes Reference Table -->
		<div class="gw-section-card" style="margin-top: 30px;">
			<h2><?php esc_html_e( 'Complete Shortcodes Reference Guide', 'gowheels' ); ?></h2>
			<table class="widefat striped gw-ref-table">
				<thead>
					<tr>
						<th style="width: 25%;"><?php esc_html_e( 'Shortcode Tag', 'gowheels' ); ?></th>
						<th style="width: 25%;"><?php esc_html_e( 'Key Attributes', 'gowheels' ); ?></th>
						<th style="width: 35%;"><?php esc_html_e( 'Description & Purpose', 'gowheels' ); ?></th>
						<th style="width: 15%;"><?php esc_html_e( 'Action', 'gowheels' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><code>[gowheels_cars]</code></td>
						<td><code>card_style="classic|modern|horizontal"</code><br><code>columns="1|2|3|4"</code><br><code>limit="12"</code><br><code>layout="grid|tabs"</code></td>
						<td><?php esc_html_e( 'Primary vehicle catalog shortcode. Displays the fleet grid with choice of 3 card styles and customizable columns.', 'gowheels' ); ?></td>
						<td>
							<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars card_style="modern" columns="3" limit="6"]'>
								<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
							</button>
						</td>
					</tr>
					<tr>
						<td><code>[gowheels_cars_tabs]</code></td>
						<td><code>card_style="modern|classic|horizontal"</code><br><code>limit="9"</code></td>
						<td><?php esc_html_e( 'Interactive fleet browser with instant category & filter tabs and live count badges.', 'gowheels' ); ?></td>
						<td>
							<button type="button" class="button gw-copy-btn" data-copy='[gowheels_cars_tabs card_style="modern"]'>
								<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
							</button>
						</td>
					</tr>
					<tr>
						<td><code>[gowheels_car]</code></td>
						<td><code>template="modern|tabs|classic"</code><br><code>id="123"</code> (optional)</td>
						<td><?php esc_html_e( 'Detailed vehicle showcase. Supports 3 templates: Modern Split (1), Executive Tabs (2), and Luxury Classic Grid (3).', 'gowheels' ); ?></td>
						<td>
							<button type="button" class="button gw-copy-btn" data-copy='[gowheels_car template="modern"]'>
								<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
							</button>
						</td>
					</tr>
					<tr>
						<td><code>[gowheels_search]</code></td>
						<td><code>action="url"</code> (optional)</td>
						<td><?php esc_html_e( 'Vehicle search and date filter bar with pickup/dropoff date-time pickers and branch location selector.', 'gowheels' ); ?></td>
						<td>
							<button type="button" class="button gw-copy-btn" data-copy='[gowheels_search]'>
								<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
							</button>
						</td>
					</tr>
					<tr>
						<td><code>[gowheels_booking]</code></td>
						<td><code>vehicle_id="123"</code> (optional)</td>
						<td><?php esc_html_e( 'Standalone booking reservation form with customer details, license upload, extras, and deposit calculation.', 'gowheels' ); ?></td>
						<td>
							<button type="button" class="button gw-copy-btn" data-copy='[gowheels_booking]'>
								<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
							</button>
						</td>
					</tr>
					<tr>
						<td><code>[gowheels_booking_status]</code></td>
						<td>—</td>
						<td><?php esc_html_e( 'Public booking verification and status tracking lookup form using booking reference number.', 'gowheels' ); ?></td>
						<td>
							<button type="button" class="button gw-copy-btn" data-copy='[gowheels_booking_status]'>
								<span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy', 'gowheels' ); ?>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

</div>
