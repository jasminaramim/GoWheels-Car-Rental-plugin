<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Shortcodes {

	public static function init() {
		foreach ( array( 'search', 'cars', 'cars_tabs', 'car', 'single', 'booking', 'booking_status', 'my_bookings' ) as $tag ) {
			add_shortcode( 'gowheels_' . $tag, array( __CLASS__, $tag ) );
		}
	}

	/* ---------- URL helpers ---------- */

	private static function page_url( $key ) {
		$id = (int) GoWheels_Settings::get( $key );
		return $id ? get_permalink( $id ) : home_url( '/' );
	}

	public static function book_url( $vehicle_id, $values ) {
		$args = array( 'vehicle_id' => (int) $vehicle_id );
		foreach ( array( 'gw_pickup', 'gw_return', 'gw_pickup_location', 'gw_return_location' ) as $k ) {
			if ( ! empty( $values[ $k ] ) ) {
				$args[ $k ] = $values[ $k ];
			}
		}
		return add_query_arg( $args, self::page_url( 'booking_page_id' ) );
	}

	/** Sanitised search values from the query string. */
	public static function search_values() {
		$out = array();
		foreach ( array( 'gw_pickup', 'gw_return' ) as $k ) {
			$v       = isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$out[ $k ] = preg_match( '/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}$/', $v ) ? str_replace( ' ', 'T', $v ) : '';
		}
		foreach ( array( 'gw_pickup_location', 'gw_return_location' ) as $k ) {
			$out[ $k ] = isset( $_GET[ $k ] ) ? absint( $_GET[ $k ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		}
		$out['vehicle_id'] = isset( $_GET['vehicle_id'] ) ? absint( $_GET['vehicle_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		return $out;
	}

	/* ---------- Shortcodes ---------- */

	public static function search( $atts = array() ) {
		GoWheels_Assets::enqueue();
		$atts = shortcode_atts( array( 'action' => '' ), $atts, 'gowheels_search' );
		$url  = $atts['action'] ? esc_url_raw( $atts['action'] ) : self::page_url( 'search_page_id' );
		return GoWheels_Util::template(
			'search-form',
			array(
				'action'    => $url,
				'plain'     => ! get_option( 'permalink_structure' ),
				'page_id'   => (int) GoWheels_Settings::get( 'search_page_id' ),
				'locations' => GoWheels_Util::locations(),
				'values'    => self::search_values(),
				'min'       => wp_date( 'Y-m-d\TH:i' ),
			)
		);
	}

	/**
	 * Fleet vehicles grid or tabbed listing.
	 *
	 * Attributes:
	 *  - limit: Number of vehicles to show (default 12)
	 *  - columns: Number of columns (1 to 4, default 3)
	 *  - design: Preset number ('1', '2', or '3')
	 *  - template / card_style: Card design - 'modern', 'classic', or 'compact' (default from settings)
	 *  - layout: 'grid' or 'tabs' (default 'grid')
	 */
	public static function cars( $atts = array() ) {
		GoWheels_Assets::enqueue();
		$atts = shortcode_atts(
			array(
				'limit'      => 12,
				'columns'    => 3,
				'design'     => '',
				'template'   => '',
				'card_style' => '',
				'layout'     => 'grid',
			),
			$atts,
			'gowheels_cars'
		);

		// Resolve design attribute: 1 -> modern, 2 -> classic, 3 -> compact
		$design = ! empty( $atts['design'] ) ? sanitize_key( $atts['design'] ) : '';
		if ( ! empty( $design ) && in_array( $design, array( '1', '2', '3' ), true ) ) {
			$design_map = array(
				'1' => 'modern',
				'2' => 'classic',
				'3' => 'compact',
			);
			$card_tpl   = $design_map[ $design ];
		} elseif ( ! empty( $atts['card_style'] ) || ! empty( $atts['template'] ) ) {
			$card_tpl = ! empty( $atts['card_style'] ) ? sanitize_key( $atts['card_style'] ) : sanitize_key( $atts['template'] );
			if ( 'horizontal' === $card_tpl ) {
				$card_tpl = 'compact';
			}
		} else {
			// Read default preset from plugin settings (defaults to '1' -> modern)
			$default_design = (string) GoWheels_Settings::get( 'default_card_design' );
			$design_map     = array(
				'1' => 'modern',
				'2' => 'classic',
				'3' => 'compact',
			);
			$card_tpl       = isset( $design_map[ $default_design ] ) ? $design_map[ $default_design ] : 'modern';
		}

		if ( ! in_array( $card_tpl, array( 'classic', 'modern', 'compact', 'horizontal' ), true ) ) {
			$card_tpl = 'modern';
		}

		$layout = sanitize_key( $atts['layout'] );
		$cols   = min( 4, max( 1, absint( $atts['columns'] ) ) );
		$vals   = self::search_values();
		$cars   = array();
		$msg    = '';

		if ( $vals['gw_pickup'] && $vals['gw_return'] ) {
			$dates = GoWheels_Booking_Validator::parse_dates( $vals['gw_pickup'], $vals['gw_return'] );
			if ( is_wp_error( $dates ) ) {
				$msg = $dates->get_error_message();
			} else {
				foreach ( GoWheels_Availability::available_vehicle_ids( $dates['pickup_utc'], $dates['return_utc'], $vals['gw_pickup_location'] ) as $id ) {
					$q = GoWheels_Pricing::quote( $id, $dates['pickup_utc'], $dates['return_utc'] );
					if ( is_wp_error( $q ) ) {
						continue;
					}
					$d          = GoWheels_Vehicle_Service::data( $id );
					$d['quote'] = $q;
					$cars[]     = $d;
				}
				if ( ! $cars ) {
					$msg = __( 'No vehicles are available for those dates. Please try different dates.', 'gowheels' );
				}
			}
		} else {
			$cars = GoWheels_Vehicle_Service::all( absint( $atts['limit'] ) ? absint( $atts['limit'] ) : 12 );
			if ( ! $cars ) {
				$msg = __( 'No vehicles have been published yet.', 'gowheels' );
			}
		}

		// If layout="tabs", render the interactive tabbed fleet template
		if ( 'tabs' === $layout && ! empty( $cars ) ) {
			return GoWheels_Util::template(
				'cars-tabs',
				array(
					'cars'          => $cars,
					'card_template' => $card_tpl,
					'book_url_base' => self::page_url( 'booking_page_id' ),
					'search_vals'   => $vals,
					'cols'          => $cols,
				)
			);
		}

		$html = '<div class="gw-wrap gw-fleet">';
		if ( $msg ) {
			$html .= '<p class="gw-notice" role="status">' . esc_html( $msg ) . '</p>';
		}

		$grid_class = in_array( $card_tpl, array( 'compact', 'horizontal' ), true )
			? 'gw-grid gw-cols-1 gw-compact-list gw-horizontal-list'
			: 'gw-grid gw-cols-' . (int) $cols;
		$html      .= '<div class="' . esc_attr( $grid_class ) . '">';

		if ( 'modern' === $card_tpl ) {
			$card_file = 'car-card-modern';
		} elseif ( 'classic' === $card_tpl ) {
			$card_file = 'car-card-classic';
		} elseif ( in_array( $card_tpl, array( 'compact', 'horizontal' ), true ) ) {
			$card_file = 'car-card-compact';
		} else {
			$card_file = 'car-card-modern';
		}

		foreach ( $cars as $car ) {
			$html .= GoWheels_Util::template(
				$card_file,
				array(
					'car'      => $car,
					'quote'    => isset( $car['quote'] ) ? $car['quote'] : null,
					'book_url' => self::book_url( $car['id'], $vals ),
				)
			);
		}
		return $html . '</div></div>';
	}

	/**
	 * Dedicated shortcut shortcode for Tabbed Fleet Layout: [gowheels_cars_tabs]
	 */
	public static function cars_tabs( $atts = array() ) {
		$atts = is_array( $atts ) ? $atts : array();
		$atts['layout'] = 'tabs';
		if ( empty( $atts['template'] ) && empty( $atts['card_style'] ) && empty( $atts['design'] ) ) {
			$atts['design'] = '1';
		}
		return self::cars( $atts );
	}

	/**
	 * Single Vehicle Display shortcode: [gowheels_single] or [gowheels_car].
	 *
	 * Attributes:
	 *  - id: Vehicle post ID (optional if on single car page)
	 *  - design: Preset number ('1', '2', or '3')
	 *  - template / style: Single car template ('classic', 'luxury', 'split')
	 */
	public static function car( $atts = array() ) {
		GoWheels_Assets::enqueue();
		$atts = shortcode_atts(
			array(
				'id'       => 0,
				'design'   => '',
				'template' => '',
				'style'    => '',
			),
			$atts,
			'gowheels_car'
		);

		// Resolve design attribute: 1 -> classic, 2 -> luxury, 3 -> split
		$design = ! empty( $atts['design'] ) ? sanitize_key( $atts['design'] ) : '';
		if ( ! empty( $design ) && in_array( $design, array( '1', '2', '3' ), true ) ) {
			$design_map = array(
				'1' => 'classic',
				'2' => 'luxury',
				'3' => 'split',
			);
			$tpl        = $design_map[ $design ];
		} elseif ( ! empty( $atts['style'] ) || ! empty( $atts['template'] ) ) {
			$tpl = ! empty( $atts['style'] ) ? sanitize_key( $atts['style'] ) : sanitize_key( $atts['template'] );
		} else {
			// Read default preset from plugin settings (defaults to '1' -> classic)
			$default_single = (string) GoWheels_Settings::get( 'default_single_design' );
			$design_map     = array(
				'1' => 'classic',
				'2' => 'luxury',
				'3' => 'split',
			);
			$tpl            = isset( $design_map[ $default_single ] ) ? $design_map[ $default_single ] : 'classic';
		}

		// Normalize aliases: modern -> classic, tabs -> split
		if ( 'modern' === $tpl ) {
			$tpl = 'classic';
		} elseif ( 'tabs' === $tpl ) {
			$tpl = 'split';
		}

		if ( ! in_array( $tpl, array( 'classic', 'luxury', 'split' ), true ) ) {
			$tpl = 'classic';
		}

		$id = absint( $atts['id'] );
		if ( ! $id && is_singular( GoWheels_Vehicle_CPT::TYPE ) ) {
			$id = get_the_ID();
		}
		$car = $id ? GoWheels_Vehicle_Service::data( $id ) : null;
		if ( ! $car ) {
			return '';
		}

		$tpl_map  = array(
			'classic' => 'single-classic', // Single Design 1 — Classic Detail
			'luxury'  => 'single-luxury',  // Single Design 2 — Luxury Detail
			'split'   => 'single-split',   // Single Design 3 — Split Layout
		);
		$tpl_file = isset( $tpl_map[ $tpl ] ) ? $tpl_map[ $tpl ] : 'single-classic';

		return GoWheels_Util::template(
			$tpl_file,
			array(
				'car'      => $car,
				'book_url' => self::book_url( $id, self::search_values() ),
			)
		);
	}

	public static function single( $atts = array() ) {
		return self::car( $atts );
	}

	public static function booking( $atts = array() ) {
		GoWheels_Assets::enqueue();
		$vals = self::search_values();
		$s    = GoWheels_Settings::all();
		return GoWheels_Util::template(
			'booking-form',
			array(
				'vehicles'  => GoWheels_Vehicle_Service::all(),
				'values'    => $vals,
				'extras'    => GoWheels_Pricing::extras(),
				'locations' => GoWheels_Util::locations(),
				'online'    => GoWheels_Payments::online_available(),
				'policy'    => $s['cancellation_policy'],
				'hold'      => (int) $s['hold_minutes'],
				'min'       => wp_date( 'Y-m-d\TH:i' ),
			)
		);
	}

	public static function booking_status( $atts = array() ) {
		GoWheels_Assets::enqueue();
		$ref   = isset( $_GET['gw_ref'] ) ? sanitize_text_field( wp_unslash( $_GET['gw_ref'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$token = isset( $_GET['gw_token'] ) ? sanitize_text_field( wp_unslash( $_GET['gw_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$paid  = isset( $_GET['gw_paid'] ) ? sanitize_key( wp_unslash( $_GET['gw_paid'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( '' === $ref ) {
			return '<div class="gw-wrap"><p class="gw-notice">' . esc_html__( 'Open the link from your booking email to see your booking status.', 'gowheels' ) . '</p></div>';
		}
		$b = GoWheels_Booking::get_by_ref( $ref );
		if ( ! $b || ! GoWheels_Util::check_token( $b->booking_ref, $token ) ) {
			return '<div class="gw-wrap"><p class="gw-notice gw-error" role="alert">' . esc_html__( 'Booking not found. Please check your link.', 'gowheels' ) . '</p></div>';
		}
		return GoWheels_Util::template(
			'booking-confirmation',
			array(
				'view'  => GoWheels_Booking::public_view( $b ),
				'ref'   => $b->booking_ref,
				'token' => $token,
				'paid'  => $paid,
				'hold'  => (int) GoWheels_Settings::get( 'hold_minutes' ),
			)
		);
	}

	public static function my_bookings( $atts = array() ) {
		GoWheels_Assets::enqueue();
		if ( ! is_user_logged_in() ) {
			return '<div class="gw-wrap"><p class="gw-notice">' . sprintf(
				/* translators: %s login link */
				esc_html__( 'Please %s to see your bookings.', 'gowheels' ),
				'<a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . esc_html__( 'log in', 'gowheels' ) . '</a>'
			) . '</p></div>';
		}
		global $wpdb;
		$t    = GoWheels_Database::bookings();
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} WHERE customer_user_id = %d ORDER BY id DESC LIMIT 50", get_current_user_id() ) ); // phpcs:ignore WordPress.DB.PreparedSQL
		$html = '<div class="gw-wrap"><h2>' . esc_html__( 'My bookings', 'gowheels' ) . '</h2>';
		if ( ! $rows ) {
			return $html . '<p>' . esc_html__( 'You have no bookings yet.', 'gowheels' ) . '</p></div>';
		}
		$html .= '<div class="gw-table-wrap"><table class="gw-table"><thead><tr><th>' . esc_html__( 'Reference', 'gowheels' ) . '</th><th>' . esc_html__( 'Vehicle', 'gowheels' ) . '</th><th>' . esc_html__( 'Pickup', 'gowheels' ) . '</th><th>' . esc_html__( 'Status', 'gowheels' ) . '</th><th>' . esc_html__( 'Total', 'gowheels' ) . '</th></tr></thead><tbody>';
		foreach ( $rows as $b ) {
			$html .= sprintf(
				'<tr><td><a href="%s">%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
				esc_url( GoWheels_Util::status_url( $b ) ),
				esc_html( $b->booking_ref ),
				esc_html( get_the_title( $b->vehicle_id ) ),
				esc_html( GoWheels_Util::to_local( $b->pickup_at_utc ) ),
				GoWheels_Booking_Status::badge( $b->status ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( GoWheels_Util::money( $b->grand_total ) )
			);
		}
		return $html . '</tbody></table></div></div>';
	}
}
