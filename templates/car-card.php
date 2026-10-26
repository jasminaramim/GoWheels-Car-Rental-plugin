<?php
defined( 'ABSPATH' ) || exit;
/** @var array $car @var array|null $quote @var string $book_url */
?>
<article class="gw-card">
	<?php if ( ! empty( $car['image'] ) ) : ?>
		<a class="gw-card-img" href="<?php echo esc_url( $car['permalink'] ); ?>"><img src="<?php echo esc_url( $car['image'] ); ?>" alt="<?php echo esc_attr( $car['title'] ); ?>" loading="lazy"></a>
	<?php endif; ?>
	<div class="gw-card-body">
		<h3 class="gw-card-title"><a href="<?php echo esc_url( $car['permalink'] ); ?>"><?php echo esc_html( $car['title'] ); ?></a></h3>
		<ul class="gw-specs">
			<?php if ( $car['seats'] ) : ?><li><?php printf( /* translators: %d seats */ esc_html__( '%d seats', 'gowheels' ), (int) $car['seats'] ); ?></li><?php endif; ?>
			<?php if ( $car['transmission'] ) : ?><li><?php echo esc_html( $car['transmission'] ); ?></li><?php endif; ?>
			<?php if ( $car['fuel_type'] ) : ?><li><?php echo esc_html( $car['fuel_type'] ); ?></li><?php endif; ?>
			<?php if ( $car['luggage'] ) : ?><li><?php printf( /* translators: %d bags */ esc_html__( '%d bags', 'gowheels' ), (int) $car['luggage'] ); ?></li><?php endif; ?>
		</ul>
		<p class="gw-price">
			<strong><?php echo esc_html( GoWheels_Util::money( $car['price_daily'] ) ); ?></strong> <span><?php esc_html_e( '/ day', 'gowheels' ); ?></span>
		</p>
		<?php if ( $quote ) : ?>
			<p class="gw-quote-line"><?php esc_html_e( 'Total for your dates:', 'gowheels' ); ?> <strong><?php echo esc_html( GoWheels_Util::money( $quote['grand_total'] ) ); ?></strong></p>
		<?php endif; ?>
		<a class="gw-btn" href="<?php echo esc_url( $book_url ); ?>"><?php esc_html_e( 'Book now', 'gowheels' ); ?></a>
	</div>
</article>
