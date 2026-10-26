<?php
defined( 'ABSPATH' ) || exit;

/**
 * Payment gateway contract. Add new providers (SSLCommerz, bKash, PayPal…)
 * by implementing this interface and registering via the `gowheels_gateways` filter.
 */
interface GoWheels_Gateway {

	public function id();

	public function label();

	public function is_enabled();

	/**
	 * Create a hosted checkout session.
	 *
	 * @param object $booking Booking row.
	 * @param object $payment Pending payment row.
	 * @return array|WP_Error ['url' => string, 'transaction_id' => string]
	 */
	public function create_session( $booking, $payment );

	/**
	 * Verify and process a provider webhook.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_webhook( WP_REST_Request $request );
}
