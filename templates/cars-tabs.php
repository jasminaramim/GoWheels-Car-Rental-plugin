<?php
defined( 'ABSPATH' ) || exit;
/**
 * Tabbed Fleet View Template
 *
 * @var array  $cars
 * @var string $card_template
 * @var string $book_url_base
 * @var array  $search_vals
 * @var int    $cols
 */

// Build dynamic categories/tabs from current vehicle dataset
$tabs = array(
	'all' => array(
		'label' => __( 'All Fleet', 'gowheels' ),
		'cars'  => $cars,
	),
);

// Collect transmissions and fuels
$auto_cars   = array();
$manual_cars = array();
$eco_cars    = array();

foreach ( $cars as $c ) {
	$trans = strtolower( (string) $c['transmission'] );
	$fuel  = strtolower( (string) $c['fuel_type'] );

	if ( false !== strpos( $trans, 'auto' ) ) {
		$auto_cars[] = $c;
	} elseif ( false !== strpos( $trans, 'manual' ) ) {
		$manual_cars[] = $c;
	}

	if ( false !== strpos( $fuel, 'hybrid' ) || false !== strpos( $fuel, 'electric' ) ) {
		$eco_cars[] = $c;
	}
}

if ( ! empty( $auto_cars ) ) {
	$tabs['automatic'] = array(
		'label' => __( 'Automatic', 'gowheels' ),
		'cars'  => $auto_cars,
	);
}
if ( ! empty( $manual_cars ) ) {
	$tabs['manual'] = array(
		'label' => __( 'Manual', 'gowheels' ),
		'cars'  => $manual_cars,
	);
}
if ( ! empty( $eco_cars ) ) {
	$tabs['eco'] = array(
		'label' => __( 'Hybrid & EV', 'gowheels' ),
		'cars'  => $eco_cars,
	);
}

$card_template_file = 'modern' === $card_template ? 'car-card-modern' : ( 'horizontal' === $card_template ? 'car-card-horizontal' : 'car-card' );
?>
<div class="gw-wrap gw-tabs-container gw-fleet-tabs">
	<!-- Tab Navigation Bar -->
	<nav class="gw-tab-nav" role="tablist" aria-label="<?php esc_attr_e( 'Vehicle Categories', 'gowheels' ); ?>">
		<?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
			<button
				type="button"
				role="tab"
				class="gw-tab-btn <?php echo $first ? 'is-active' : ''; ?>"
				data-gw-tab="<?php echo esc_attr( $key ); ?>"
				aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
				aria-controls="gw-tab-panel-<?php echo esc_attr( $key ); ?>"
			>
				<span class="gw-tab-label"><?php echo esc_html( $tab['label'] ); ?></span>
				<span class="gw-tab-count"><?php echo count( $tab['cars'] ); ?></span>
			</button>
		<?php $first = false; endforeach; ?>
	</nav>

	<!-- Tab Panels -->
	<div class="gw-tab-panels">
		<?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
			<div
				id="gw-tab-panel-<?php echo esc_attr( $key ); ?>"
				class="gw-tab-panel <?php echo $first ? 'is-active' : ''; ?>"
				role="tabpanel"
				<?php echo $first ? '' : 'hidden'; ?>
			>
				<?php if ( empty( $tab['cars'] ) ) : ?>
					<p class="gw-notice"><?php esc_html_e( 'No vehicles found in this category.', 'gowheels' ); ?></p>
				<?php else : ?>
					<div class="gw-grid <?php echo 'horizontal' === $card_template ? 'gw-cols-1' : 'gw-cols-' . (int) $cols; ?>">
						<?php foreach ( $tab['cars'] as $car ) : ?>
							<?php
							echo GoWheels_Util::template(
								$card_template_file,
								array(
									'car'      => $car,
									'quote'    => isset( $car['quote'] ) ? $car['quote'] : null,
									'book_url' => GoWheels_Shortcodes::book_url( $car['id'], $search_vals ),
								)
							);
							?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php $first = false; endforeach; ?>
	</div>
</div>
