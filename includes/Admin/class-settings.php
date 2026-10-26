<?php
defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings (single option array) + settings page.
 */
class GoWheels_Settings {

	const OPTION = 'gowheels_settings';
	const GROUP  = 'gowheels_settings_group';

	private static $cache = null;

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_filter( 'option_page_capability_' . self::GROUP, function () {
			return GoWheels_Capabilities::MANAGE;
		} );
	}

	public static function defaults() {
		return array(
			'currency_code'       => 'BDT',
			'currency_symbol'     => '৳',
			'display_decimals'    => 2,
			'admin_email'         => get_option( 'admin_email' ),
			'search_page_id'      => 0,
			'booking_page_id'     => 0,
			'status_page_id'      => 0,
			'hold_minutes'        => 30,
			'buffer_minutes'      => 60,
			'grace_minutes'       => 60,
			'min_lead_hours'      => 2,
			'max_rental_days'     => 30,
			'allow_past'          => 0,
			'cancellation_policy' => '',
			'tax_percent'         => 0,
			'discount_min_days'   => 7,
			'discount_percent'    => 0,
			'deposit_mode'        => 'percent',
			'deposit_value'       => 20,
			'extras'              => "Extra driver|500|flat\nChild seat|200|day\nAirport pickup|1000|flat",
			'online_enabled'      => 0,
			'stripe_secret'       => '',
			'stripe_webhook_secret' => '',
			'default_card_design'   => '1',
			'default_single_design' => '1',
			'delete_on_uninstall' => 0,
		);
	}

	public static function all() {
		if ( null === self::$cache ) {
			self::$cache = wp_parse_args( get_option( self::OPTION, array() ), self::defaults() );
		}
		return self::$cache;
	}

	public static function get( $key ) {
		$all = self::all();
		return isset( $all[ $key ] ) ? $all[ $key ] : null;
	}

	public static function update( array $values ) {
		$current = get_option( self::OPTION, array() );
		update_option( self::OPTION, array_merge( is_array( $current ) ? $current : array(), $values ) );
		self::$cache = null;
	}

	/** Field definitions: key => [label, type, description, options] */
	public static function fields() {
		return array(
			__( 'General', 'gowheels' ) => array(
				'currency_code'    => array( __( 'Currency code', 'gowheels' ), 'text', 'e.g. BDT, USD' ),
				'currency_symbol'  => array( __( 'Currency symbol', 'gowheels' ), 'text', '' ),
				'display_decimals' => array( __( 'Decimals shown', 'gowheels' ), 'number', '' ),
				'admin_email'      => array( __( 'Notification email', 'gowheels' ), 'email', __( 'New booking requests are sent here.', 'gowheels' ) ),
				'search_page_id'   => array( __( 'Fleet / search page', 'gowheels' ), 'page', '' ),
				'booking_page_id'  => array( __( 'Booking form page', 'gowheels' ), 'page', '' ),
				'status_page_id'   => array( __( 'Booking status page', 'gowheels' ), 'page', '' ),
			),
			__( 'Booking rules', 'gowheels' ) => array(
				'hold_minutes'        => array( __( 'Temporary hold (minutes)', 'gowheels' ), 'number', __( 'A new request blocks the car for this long while waiting for approval. 0 = no hold.', 'gowheels' ) ),
				'buffer_minutes'      => array( __( 'Buffer between bookings (minutes)', 'gowheels' ), 'number', __( 'Cleaning / turnaround time before and after each booking.', 'gowheels' ) ),
				'grace_minutes'       => array( __( 'Late-return grace (minutes)', 'gowheels' ), 'number', __( 'Each 24h block starts a new rental day after this grace period.', 'gowheels' ) ),
				'min_lead_hours'      => array( __( 'Minimum notice (hours)', 'gowheels' ), 'number', '' ),
				'max_rental_days'     => array( __( 'Maximum rental (days)', 'gowheels' ), 'number', __( '0 = unlimited.', 'gowheels' ) ),
				'allow_past'          => array( __( 'Allow past-dated bookings', 'gowheels' ), 'checkbox', __( 'Not recommended for public booking.', 'gowheels' ) ),
				'cancellation_policy' => array( __( 'Cancellation / deposit policy text', 'gowheels' ), 'textarea', __( 'Shown on the booking form.', 'gowheels' ) ),
			),
			__( 'Pricing & deposit', 'gowheels' ) => array(
				'tax_percent'       => array( __( 'Tax (%)', 'gowheels' ), 'number', '' ),
				'discount_min_days' => array( __( 'Long-rental discount from (days)', 'gowheels' ), 'number', '' ),
				'discount_percent'  => array( __( 'Long-rental discount (%)', 'gowheels' ), 'number', __( 'Applied to the rental cost. 0 = off.', 'gowheels' ) ),
				'deposit_mode'      => array( __( 'Deposit mode', 'gowheels' ), 'select', '', array( 'percent' => __( 'Percentage of total', 'gowheels' ), 'fixed' => __( 'Fixed amount', 'gowheels' ) ) ),
				'deposit_value'     => array( __( 'Deposit value', 'gowheels' ), 'number', __( 'Percent (e.g. 20) or fixed amount depending on mode.', 'gowheels' ) ),
				'extras'            => array( __( 'Extra services', 'gowheels' ), 'textarea', __( 'One per line: Name|price|flat or Name|price|day', 'gowheels' ) ),
			),
			__( 'Online payments (Stripe Checkout)', 'gowheels' ) => array(
				'online_enabled'        => array( __( 'Enable online payment', 'gowheels' ), 'checkbox', '' ),
				'stripe_secret'         => array( __( 'Stripe secret key', 'gowheels' ), 'password', __( 'Leave blank to keep the saved key.', 'gowheels' ) ),
				'stripe_webhook_secret' => array(
					__( 'Stripe webhook signing secret', 'gowheels' ),
					'password',
					sprintf(
						/* translators: %s webhook URL */
						__( 'Webhook endpoint: %s (event: checkout.session.completed). Leave blank to keep the saved secret.', 'gowheels' ),
						esc_url( rest_url( 'gowheels/v1/payments/webhook/stripe' ) )
					),
				),
			),
			__( 'Design Studio Presets', 'gowheels' ) => array(
				'default_card_design'   => array(
					__( 'Default Car Card Design', 'gowheels' ),
					'select',
					__( 'Default preset for [gowheels_cars] shortcode and vehicle catalog.', 'gowheels' ),
					array(
						'1' => __( 'Design 1 — Modern Card (Clean & Modern)', 'gowheels' ),
						'2' => __( 'Design 2 — Classic Card (Detailed & Informative)', 'gowheels' ),
						'3' => __( 'Design 3 — Compact Card (Horizontal & Stylish)', 'gowheels' ),
					),
				),
				'default_single_design' => array(
					__( 'Default Single Rental Design', 'gowheels' ),
					'select',
					__( 'Default preset for single vehicle pages and [gowheels_single] shortcode.', 'gowheels' ),
					array(
						'1' => __( 'Design 1 — Classic Detail (Gallery & Booking Sidebar)', 'gowheels' ),
						'2' => __( 'Design 2 — Luxury Detail (Hero Image & VIP Experience)', 'gowheels' ),
						'3' => __( 'Design 3 — Split Layout (Interactive Tabs & Compact)', 'gowheels' ),
					),
				),
			),
			__( 'Data', 'gowheels' ) => array(
				'delete_on_uninstall' => array( __( 'Delete ALL plugin data on uninstall', 'gowheels' ), 'checkbox', __( 'Bookings, payments, vehicles and settings are permanently removed when the plugin is deleted.', 'gowheels' ) ),
			),
		);
	}

	public static function register() {
		register_setting(
			self::GROUP,
			self::OPTION,
			array( 'type' => 'array', 'sanitize_callback' => array( __CLASS__, 'sanitize' ) )
		);
	}

	public static function sanitize( $input ) {
		$input    = is_array( $input ) ? $input : array();
		$defaults = self::defaults();
		$old      = get_option( self::OPTION, array() );
		$out      = wp_parse_args( is_array( $old ) ? $old : array(), $defaults );

		foreach ( self::fields() as $fields ) {
			foreach ( $fields as $key => $f ) {
				$raw = isset( $input[ $key ] ) ? $input[ $key ] : null;
				switch ( $f[1] ) {
					case 'checkbox':
						$out[ $key ] = empty( $raw ) ? 0 : 1;
						break;
					case 'number':
						$out[ $key ] = is_numeric( $raw ) ? max( 0, (float) $raw ) : $defaults[ $key ];
						break;
					case 'email':
						$e           = sanitize_email( (string) $raw );
						$out[ $key ] = is_email( $e ) ? $e : $defaults[ $key ];
						break;
					case 'textarea':
						$out[ $key ] = sanitize_textarea_field( (string) $raw );
						break;
					case 'select':
						$out[ $key ] = ( null !== $raw && isset( $f[3][ $raw ] ) ) ? $raw : $defaults[ $key ];
						break;
					case 'page':
						$out[ $key ] = absint( $raw );
						break;
					case 'password':
						$v = trim( sanitize_text_field( (string) $raw ) );
						if ( '' !== $v ) {
							$out[ $key ] = $v;
						}
						break;
					default:
						$out[ $key ] = sanitize_text_field( (string) $raw );
				}
			}
		}
		self::$cache = null;
		return $out;
	}

	public static function render_page() {
		if ( ! current_user_can( GoWheels_Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'gowheels' ) );
		}
		$values = self::all();
		echo '<div class="wrap"><h1>' . esc_html__( 'GoWheels Settings', 'gowheels' ) . '</h1>';
		echo '<form method="post" action="options.php">';
		settings_fields( self::GROUP );
		foreach ( self::fields() as $section => $fields ) {
			echo '<h2>' . esc_html( $section ) . '</h2><table class="form-table" role="presentation">';
			foreach ( $fields as $key => $f ) {
				$name = self::OPTION . '[' . $key . ']';
				$id   = 'gw_' . $key;
				echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
				$val = isset( $values[ $key ] ) ? $values[ $key ] : '';
				switch ( $f[1] ) {
					case 'checkbox':
						printf( '<input type="checkbox" id="%s" name="%s" value="1" %s>', esc_attr( $id ), esc_attr( $name ), checked( (int) $val, 1, false ) );
						break;
					case 'textarea':
						printf( '<textarea id="%s" name="%s" rows="5" class="large-text">%s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $val ) );
						break;
					case 'select':
						echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
						foreach ( $f[3] as $k => $label ) {
							printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $val, $k, false ), esc_html( $label ) );
						}
						echo '</select>';
						break;
					case 'page':
						wp_dropdown_pages( array( 'name' => $name, 'id' => $id, 'selected' => (int) $val, 'show_option_none' => __( '— Select —', 'gowheels' ) ) );
						break;
					case 'password':
						printf( '<input type="password" id="%s" name="%s" value="" class="regular-text" autocomplete="new-password" placeholder="%s">', esc_attr( $id ), esc_attr( $name ), $val ? esc_attr__( '•••••• saved', 'gowheels' ) : '' );
						break;
					case 'number':
						printf( '<input type="number" step="any" min="0" id="%s" name="%s" value="%s" class="small-text">', esc_attr( $id ), esc_attr( $name ), esc_attr( $val ) );
						break;
					default:
						printf( '<input type="%s" id="%s" name="%s" value="%s" class="regular-text">', esc_attr( 'email' === $f[1] ? 'email' : 'text' ), esc_attr( $id ), esc_attr( $name ), esc_attr( $val ) );
				}
				if ( ! empty( $f[2] ) ) {
					echo '<p class="description">' . wp_kses_post( $f[2] ) . '</p>';
				}
				echo '</td></tr>';
			}
			echo '</table>';
		}
		submit_button();
		echo '</form></div>';
	}
}
