<?php
defined( 'ABSPATH' ) || exit;
/** @var array $car @var string $book_url */
?>
<div class="gw-wrap gw-details">
	<?php if ( ! empty( $car['gallery'] ) || ! empty( $car['image'] ) ) : ?>
	<div class="gw-gallery">
		<?php if ( ! empty( $car['image'] ) ) : ?>
			<img src="<?php echo esc_url( $car['image'] ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>">
		<?php endif; ?>
		<?php foreach ( $car['gallery'] as $img ) : ?>
			<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ? $img['alt'] : $car['title'] ); ?>" loading="lazy">
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<h2 class="gw-details-title"><?php echo esc_html( $car['title'] ); ?></h2>
	<p class="gw-price"><strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong> <span><?php esc_html_e( '/ day', 'gowheels' ); ?></span></p>

	<h3><?php esc_html_e( 'Specifications', 'gowheels' ); ?></h3>
	<ul class="gw-specs gw-specs-lg">
		<?php if ( $car['seats'] ) : ?><li><?php printf( esc_html__( '%d seats', 'gowheels' ), (int) $car['seats'] ); ?></li><?php endif; ?>
		<?php if ( $car['transmission'] ) : ?><li><?php echo esc_html( $car['transmission'] ); ?></li><?php endif; ?>
		<?php if ( $car['fuel_type'] ) : ?><li><?php echo esc_html( $car['fuel_type'] ); ?></li><?php endif; ?>
		<?php if ( $car['luggage'] ) : ?><li><?php printf( esc_html__( '%d bags', 'gowheels' ), (int) $car['luggage'] ); ?></li><?php endif; ?>
		<?php if ( $car['location_name'] ) : ?><li><?php echo esc_html( $car['location_name'] ); ?></li><?php endif; ?>
		<?php if ( $car['min_rental'] > 1 ) : ?><li><?php printf( esc_html__( 'Minimum %d days', 'gowheels' ), (int) $car['min_rental'] ); ?></li><?php endif; ?>
	</ul>

	<?php if ( $car['features'] ) : ?>
		<h3><?php esc_html_e( 'Included features', 'gowheels' ); ?></h3>
		<ul class="gw-features">
			<?php foreach ( $car['features'] as $f ) : ?><li><?php echo esc_html( $f ); ?></li><?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<a class="gw-btn" href="<?php echo esc_url( $book_url ); ?>"><?php esc_html_e( 'Book this car', 'gowheels' ); ?></a>
</div>
