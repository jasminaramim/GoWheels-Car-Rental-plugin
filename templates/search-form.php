<?php
defined( 'ABSPATH' ) || exit;
/** @var string $action @var bool $plain @var int $page_id @var array $locations @var array $values @var string $min */
?>
<form class="gw-wrap gw-search" method="get" action="<?php echo esc_url( $action ); ?>">
	<?php if ( $plain && $page_id ) : ?>
		<input type="hidden" name="page_id" value="<?php echo (int) $page_id; ?>">
	<?php endif; ?>
	<div class="gw-search-grid">
		<?php if ( $locations ) : ?>
		<div class="gw-field-row">
			<label for="gw-s-pl"><?php esc_html_e( 'Pickup location', 'gowheels' ); ?></label>
			<select class="gw-field" id="gw-s-pl" name="gw_pickup_location" required>
				<option value=""><?php esc_html_e( 'Select…', 'gowheels' ); ?></option>
				<?php foreach ( $locations as $id => $name ) : ?>
					<option value="<?php echo (int) $id; ?>" <?php selected( (int) $values['gw_pickup_location'], (int) $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="gw-field-row">
			<label for="gw-s-rl"><?php esc_html_e( 'Return location', 'gowheels' ); ?></label>
			<select class="gw-field" id="gw-s-rl" name="gw_return_location" required>
				<option value=""><?php esc_html_e( 'Select…', 'gowheels' ); ?></option>
				<?php foreach ( $locations as $id => $name ) : ?>
					<option value="<?php echo (int) $id; ?>" <?php selected( (int) $values['gw_return_location'], (int) $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>
		<div class="gw-field-row">
			<label for="gw-s-p"><?php esc_html_e( 'Pickup date & time', 'gowheels' ); ?></label>
			<input class="gw-field" type="datetime-local" id="gw-s-p" name="gw_pickup" min="<?php echo esc_attr( $min ); ?>" value="<?php echo esc_attr( $values['gw_pickup'] ); ?>" required>
		</div>
		<div class="gw-field-row">
			<label for="gw-s-r"><?php esc_html_e( 'Return date & time', 'gowheels' ); ?></label>
			<input class="gw-field" type="datetime-local" id="gw-s-r" name="gw_return" min="<?php echo esc_attr( $min ); ?>" value="<?php echo esc_attr( $values['gw_return'] ); ?>" required>
		</div>
		<div class="gw-field-row gw-field-submit">
			<button type="submit" class="gw-btn"><?php esc_html_e( 'Search cars', 'gowheels' ); ?></button>
		</div>
	</div>
</form>
