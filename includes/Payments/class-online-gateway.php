<?php
defined( 'ABSPATH' ) || exit;

/**
 * Stripe Checkout (hosted page). Card data never touches this site.
 * Payment is only marked paid after a signature-verified webhook.
 */
class GoWheels_Stripe_Gateway implements GoWheels_Gateway {

	const API = 'https://api.stripe.com/v1/';

	public function id() {
		return 'stripe';
	}

	public function label() {
		return __( 'Card (Stripe)', 'gowheels' );
	}

	public function is_enabled() {
		return (bool) GoWheels_Settings::get( 'online_enabled' ) && '' !== (string) GoWheels_Settings::get( 'stripe_secret' );
	}

	private function minor_units( $amount, $currency ) {
		$zero = array( 'bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf' );
		return in_array( strtolower( $currency ), $zero, true ) ? (int) round( $amount ) : (int) round( $amount * 100 );
	}

	public function create_session( $booking, $payment ) {
		$currency = strtolower( $payment->currency );
		$status   = GoWheels_Util::status_url( $booking );
		$body     = array(
			'mode'                                              => 'payment',
			'success_url'                                       => add_query_arg( 'gw_paid', '1', $status ),
			'cancel_url'                                        => add_query_arg( 'gw_paid', '0', $status ),
			'client_reference_id'                               => $booking->booking_ref,
			'customer_email'                                    => $booking->customer_email,
			'line_items[0][quantity]'                           => 1,
			'line_items[0][price_data][currency]'               => $currency,
			'line_items[0][price_data][unit_amount]'            => $this->minor_units( (float) $payment->amount, $currency ),
			'line_items[0][price_data][product_data][name]'     => sprintf( 'Booking %s — %s', $booking->booking_ref, $payment->payment_type ),
			'metadata[booking_id]'                              => (string) $booking->id,
			'metadata[payment_id]'                              => (string) $payment->id,
			'payment_intent_data[metadata][payment_id]'         => (string) $payment->id,
		);

		$res = wp_remote_post(
			self::API . 'checkout/sessions',
			array(
				'timeout' => 20,
				'headers' => array(
					'Authorization'   => 'Bearer ' . GoWheels_Settings::get( 'stripe_secret' ),
					'Idempotency-Key' => $payment->idempotency_key,
				),
				'body'    => $body,
			)
		);
		if ( is_wp_error( $res ) ) {
			return new WP_Error( 'gowheels_gateway', __( 'Could not reach the payment provider. Please try again.', 'gowheels' ), array( 'status' => 502 ) );
		}
		$data = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( wp_remote_retrieve_response_code( $res ) >= 400 || empty( $data['url'] ) ) {
			return new WP_Error( 'gowheels_gateway', __( 'The payment provider rejected the request.', 'gowheels' ), array( 'status' => 502 ) );
		}
		return array( 'url' => $data['url'], 'transaction_id' => isset( $data['id'] ) ? $data['id'] : '' );
	}

	private function signature_valid( $payload, $header, $secret ) {
		if ( ! $header || ! $secret ) {
			return false;
		}
		$parts = array();
		foreach ( explode( ',', $header ) as $kv ) {
			$p = explode( '=', trim( $kv ), 2 );
			if ( 2 === count( $p ) ) {
				$parts[ $p[0] ][] = $p[1];
			}
		}
		$t = isset( $parts['t'][0] ) ? $parts['t'][0] : '';
		if ( ! ctype_digit( $t ) || abs( time() - (int) $t ) > 300 ) {
			return false;
		}
		$expected = hash_hmac( 'sha256', $t . '.' . $payload, $secret );
		foreach ( isset( $parts['v1'] ) ? $parts['v1'] : array() as $sig ) {
			if ( hash_equals( $expected, $sig ) ) {
				return true;
			}
		}
		return false;
	}

	public function handle_webhook( WP_REST_Request $request ) {
		$payload = $request->get_body();
		$secret  = (string) GoWheels_Settings::get( 'stripe_webhook_secret' );
		if ( ! $this->signature_valid( $payload, $request->get_header( 'stripe-signature' ), $secret ) ) {
			return new WP_Error( 'gowheels_bad_signature', __( 'Invalid signature.', 'gowheels' ), array( 'status' => 400 ) );
		}
		$event = json_decode( $payload, true );
		if ( ! is_array( $event ) || empty( $event['type'] ) || empty( $event['data']['object'] ) ) {
			return new WP_REST_Response( array( 'received' => true ), 200 );
		}
		$obj        = $event['data']['object'];
		$payment_id = isset( $obj['metadata']['payment_id'] ) ? absint( $obj['metadata']['payment_id'] ) : 0;
		if ( ! $payment_id ) {
			return new WP_REST_Response( array( 'received' => true ), 200 );
		}

		switch ( $event['type'] ) {
			case 'checkout.session.completed':
			case 'checkout.session.async_payment_succeeded':
				if ( isset( $obj['payment_status'] ) && 'paid' === $obj['payment_status'] ) {
					$payment = GoWheels_Payments::get( $payment_id );
					$paid    = isset( $obj['amount_total'] ) ? (int) $obj['amount_total'] : -1;
					if ( $payment && $paid === $this->minor_units( (float) $payment->amount, $payment->currency ) ) {
						$txn = ! empty( $obj['payment_intent'] ) ? $obj['payment_intent'] : ( isset( $obj['id'] ) ? $obj['id'] : '' );
						GoWheels_Payments::mark_paid( $payment_id, $txn );
					} elseif ( $payment ) {
						GoWheels_Booking::log( $payment->booking_id, 'payment_mismatch', 'Webhook amount did not match payment #' . $payment_id );
					}
				}
				break;
			case 'checkout.session.async_payment_failed':
			case 'checkout.session.expired':
				GoWheels_Payments::mark_failed( $payment_id );
				break;
		}
		return new WP_REST_Response( array( 'received' => true ), 200 );
	}
}
