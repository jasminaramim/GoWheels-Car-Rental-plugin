<?php
defined( 'ABSPATH' ) || exit;

/** Offline payments are recorded manually by an admin. */
class GoWheels_Cash_Gateway implements GoWheels_Gateway {

	public function id() {
		return 'cash';
	}

	public function label() {
		return __( 'Cash / manual', 'gowheels' );
	}

	public function is_enabled() {
		return true;
	}

	public function create_session( $booking, $payment ) {
		return new WP_Error( 'gowheels_offline', __( 'This payment method is recorded by our staff.', 'gowheels' ), array( 'status' => 400 ) );
	}

	public function handle_webhook( WP_REST_Request $request ) {
		return new WP_Error( 'gowheels_no_webhook', __( 'Not supported.', 'gowheels' ), array( 'status' => 404 ) );
	}
}
