<?php
defined( 'ABSPATH' ) || exit;
/** @var array $vehicles @var array $values @var array $extras @var array $locations @var bool $online @var string $policy @var int $hold @var string $min */
if ( ! $vehicles ) {
	echo '<div class="gw-wrap"><p class="gw-notice">' . esc_html__( 'No vehicles are available for booking yet.', 'gowheels' ) . '</p></div>';
	return;
}
?>
<form class="gw-wrap gw-booking-form" novalidate>
	<div class="gw-form-error" role="alert" aria-live="assertive" hidden></div>

	<fieldset class="gw-fieldset">
		<legend><?php esc_html_e( 'Trip details', 'gowheels' ); ?></legend>
		<div class="gw-field-row">
			<label for="gw-vehicle"><?php esc_html_e( 'Vehicle', 'gowheels' ); ?></label>
			<select class="gw-field" id="gw-vehicle" name="vehicle_id" required>
				<option value=""><?php esc_html_e( 'Select a vehicle…', 'gowheels' ); ?></option>
				<?php foreach ( $vehicles as $v ) : ?>
					<option value="<?php echo (int) $v['id']; ?>" <?php selected( (int) $values['vehicle_id'], (int) $v['id'] ); ?>><?php echo esc_html( $v['title'] . ' — ' . GoWheels_Util::money( $v['price_daily'] ) . ' / ' . __( 'day', 'gowheels' ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php if ( $locations ) : ?>
		<div class="gw-field-row">
			<label for="gw-pl"><?php esc_html_e( 'Pickup location', 'gowheels' ); ?></label>
			<select class="gw-field" id="gw-pl" name="pickup_location" required>
				<option value=""><?php esc_html_e( 'Select…', 'gowheels' ); ?></option>
				<?php foreach ( $locations as $id => $name ) : ?>
					<option value="<?php echo (int) $id; ?>" <?php selected( (int) $values['gw_pickup_location'], (int) $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="gw-field-row">
			<label for="gw-rl"><?php esc_html_e( 'Return location', 'gowheels' ); ?></label>
			<select class="gw-field" id="gw-rl" name="return_location" required>
				<option value=""><?php esc_html_e( 'Select…', 'gowheels' ); ?></option>
				<?php foreach ( $locations as $id => $name ) : ?>
					<option value="<?php echo (int) $id; ?>" <?php selected( (int) $values['gw_return_location'], (int) $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>
		<div class="gw-field-row">
			<label for="gw-pickup"><?php esc_html_e( 'Pickup date & time', 'gowheels' ); ?></label>
			<input class="gw-field" type="datetime-local" id="gw-pickup" name="pickup" min="<?php echo esc_attr( $min ); ?>" value="<?php echo esc_attr( $values['gw_pickup'] ); ?>" required>
		</div>
		<div class="gw-field-row">
			<label for="gw-return"><?php esc_html_e( 'Return date & time', 'gowheels' ); ?></label>
			<input class="gw-field" type="datetime-local" id="gw-return" name="return" min="<?php echo esc_attr( $min ); ?>" value="<?php echo esc_attr( $values['gw_return'] ); ?>" required>
		</div>
	</fieldset>

	<?php if ( $extras ) : ?>
	<fieldset class="gw-fieldset">
		<legend><?php esc_html_e( 'Extras', 'gowheels' ); ?></legend>
		<?php foreach ( $extras as $e ) : ?>
			<label class="gw-check">
				<input type="checkbox" name="extras[]" value="<?php echo esc_attr( $e['key'] ); ?>">
				<?php echo esc_html( $e['name'] ); ?> — <?php echo esc_html( GoWheels_Util::money( $e['price'] ) ); ?><?php echo 'day' === $e['type'] ? esc_html( ' / ' . __( 'day', 'gowheels' ) ) : ''; ?>
			</label>
		<?php endforeach; ?>
	</fieldset>
	<?php endif; ?>

	<fieldset class="gw-fieldset">
		<legend><?php esc_html_e( 'Your details', 'gowheels' ); ?></legend>
		<div class="gw-field-row"><label for="gw-name"><?php esc_html_e( 'Full name', 'gowheels' ); ?></label><input class="gw-field" type="text" id="gw-name" name="name" maxlength="100" autocomplete="name" required></div>
		<div class="gw-field-row"><label for="gw-email"><?php esc_html_e( 'Email', 'gowheels' ); ?></label><input class="gw-field" type="email" id="gw-email" name="email" autocomplete="email" required></div>
		<div class="gw-field-row"><label for="gw-phone"><?php esc_html_e( 'Phone', 'gowheels' ); ?></label><input class="gw-field" type="tel" id="gw-phone" name="phone" maxlength="25" autocomplete="tel" required></div>
		<div class="gw-field-row"><label for="gw-notes"><?php esc_html_e( 'Notes (optional)', 'gowheels' ); ?></label><textarea class="gw-field" id="gw-notes" name="notes" rows="3" maxlength="1000"></textarea></div>
		<?php if ( $online ) : ?>
		<div class="gw-field-row">
			<span class="gw-label"><?php esc_html_e( 'Payment preference', 'gowheels' ); ?></span>
			<label class="gw-check"><input type="radio" name="payment_preference" value="cash" checked> <?php esc_html_e( 'Pay at pickup (cash)', 'gowheels' ); ?></label>
			<label class="gw-check"><input type="radio" name="payment_preference" value="online"> <?php esc_html_e( 'Pay deposit online after approval', 'gowheels' ); ?></label>
		</div>
		<?php endif; ?>
		<div class="gw-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
	</fieldset>

	<div class="gw-quote" aria-live="polite"></div>

	<p class="gw-note">
		<?php
		if ( $hold > 0 ) {
			/* translators: %d minutes */
			printf( esc_html__( 'This is a booking request, not a confirmation. If available, we hold the car for %d minutes while we review it.', 'gowheels' ), (int) $hold );
		} else {
			esc_html_e( 'This is a booking request, not a confirmation. We will email you once it is reviewed.', 'gowheels' );
		}
		?>
	</p>
	<?php if ( $policy ) : ?><p class="gw-note gw-policy"><?php echo nl2br( esc_html( $policy ) ); ?></p><?php endif; ?>

	<button type="submit" class="gw-btn"><?php esc_html_e( 'Send booking request', 'gowheels' ); ?></button>
</form>
