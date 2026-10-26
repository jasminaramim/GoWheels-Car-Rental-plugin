<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Email {

	public static function templates() {
		$t = array(
			'request_received' => array(
				'to'      => 'customer',
				'subject' => '[{site}] We received your booking request {ref}',
				'body'    => "Hi {name},\n\nThank you for your request. This is NOT a confirmed booking yet — our team will review it and reply shortly.\n\nBooking reference: {ref}\nVehicle: {vehicle}\nPickup: {pickup}\nReturn: {return}\nEstimated total: {total}\nDeposit (if approved): {deposit}\n\nWe are holding the vehicle for you until {hold_until}; after that the hold is released unless we extend it.\n\nTrack your request:\n{status_url}\n\n{site}",
			),
			'admin_new_request' => array(
				'to'      => 'admin',
				'subject' => '[{site}] New booking request {ref}',
				'body'    => "A new booking request needs review.\n\nReference: {ref}\nCustomer: {name} ({email}, {phone})\nVehicle: {vehicle}\nPickup: {pickup}\nReturn: {return}\nTotal: {total}\n\nReview: {admin_url}",
			),
			'approved'         => array(
				'to'      => 'customer',
				'subject' => '[{site}] Booking {ref} approved',
				'body'    => "Hi {name},\n\nGood news — your booking has been approved.\n\nBooking reference: {ref}\nVehicle: {vehicle}\nPickup: {pickup}\nReturn: {return}\nTotal: {total}\nDeposit due: {deposit}\n\nView details and payment options:\n{status_url}\n\n{site}",
			),
			'rejected'         => array(
				'to'      => 'customer',
				'subject' => '[{site}] Booking {ref} could not be accepted',
				'body'    => "Hi {name},\n\nSorry, we are unable to accept booking {ref} for {vehicle} ({pickup} → {return}).\n{note}\nYou are welcome to search for other dates or vehicles.\n\n{site}",
			),
			'cancelled'        => array(
				'to'      => 'customer',
				'subject' => '[{site}] Booking {ref} cancelled',
				'body'    => "Hi {name},\n\nBooking {ref} for {vehicle} has been cancelled.\n{note}\nIf you paid a deposit, we will contact you about any refund according to our policy.\n\n{site}",
			),
			'admin_cancelled'  => array(
				'to'      => 'admin',
				'subject' => '[{site}] Customer cancelled {ref}',
				'body'    => "{name} cancelled booking {ref} ({vehicle}, {pickup} → {return}).\n\n{admin_url}",
			),
			'expired'          => array(
				'to'      => 'customer',
				'subject' => '[{site}] Hold released for request {ref}',
				'body'    => "Hi {name},\n\nThe temporary hold on {vehicle} for request {ref} has ended and the vehicle is no longer reserved for you. We may still be able to approve your request if the vehicle is free — we will contact you. You can also submit a new request.\n\n{site}",
			),
			'payment_received' => array(
				'to'      => 'customer',
				'subject' => '[{site}] Payment received for {ref}',
				'body'    => "Hi {name},\n\nWe received a payment of {amount} for booking {ref}.\nPaid so far: {paid}\nBalance remaining: {balance}\n\nDetails: {status_url}\n\n{site}",
			),
		);
		return apply_filters( 'gowheels_email_templates', $t );
	}

	public static function vars( $b, $extra = array() ) {
		$snap_hold = $b->hold_expires_at_utc ? GoWheels_Util::to_local( $b->hold_expires_at_utc ) : '—';
		$paid      = GoWheels_Payments::net_paid( $b->id );
		$vars      = array(
			'{site}'       => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'{ref}'        => $b->booking_ref,
			'{name}'       => $b->customer_name,
			'{email}'      => $b->customer_email,
			'{phone}'      => $b->customer_phone,
			'{vehicle}'    => wp_specialchars_decode( get_the_title( $b->vehicle_id ), ENT_QUOTES ),
			'{pickup}'     => GoWheels_Util::to_local( $b->pickup_at_utc ),
			'{return}'     => GoWheels_Util::to_local( $b->return_at_utc ),
			'{total}'      => GoWheels_Util::money( $b->grand_total ),
			'{deposit}'    => GoWheels_Util::money( $b->deposit_due ),
			'{paid}'       => GoWheels_Util::money( $paid ),
			'{balance}'    => GoWheels_Util::money( max( 0, $b->grand_total - $paid ) ),
			'{hold_until}' => $snap_hold,
			'{status_url}' => GoWheels_Util::status_url( $b ),
			'{admin_url}'  => admin_url( 'admin.php?page=gowheels-bookings&view=' . (int) $b->id ),
			'{note}'       => '',
			'{amount}'     => '',
		);
		foreach ( $extra as $k => $v ) {
			$vars[ '{' . $k . '}' ] = is_numeric( $v ) ? GoWheels_Util::money( $v ) : (string) $v;
		}
		return $vars;
	}

	public static function send( $type, $booking, $extra = array() ) {
		$all = self::templates();
		if ( empty( $all[ $type ] ) || ! $booking ) {
			return false;
		}
		$tpl  = $all[ $type ];
		$vars = self::vars( $booking, $extra );

		$subject = trim( preg_replace( '/[\r\n]+/', ' ', strtr( $tpl['subject'], $vars ) ) );
		$body    = strtr( $tpl['body'], $vars );
		$body    = preg_replace( "/\n{3,}/", "\n\n", $body );

		$admin = GoWheels_Settings::get( 'admin_email' );
		$to    = ( 'admin' === $tpl['to'] ) ? $admin : $booking->customer_email;
		if ( ! is_email( $to ) ) {
			return false;
		}
		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		if ( 'customer' === $tpl['to'] && is_email( $admin ) ) {
			$headers[] = 'Reply-To: ' . $admin;
		}
		return wp_mail( $to, $subject, $body, $headers );
	}
}
