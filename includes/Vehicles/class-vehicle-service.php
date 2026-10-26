<?php
defined( 'ABSPATH' ) || exit;

class GoWheels_Vehicle_Service {

	/** Public-safe vehicle data. */
	public static function data( $id ) {
		$p = get_post( $id );
		if ( ! $p || GoWheels_Vehicle_CPT::TYPE !== $p->post_type || 'publish' !== $p->post_status ) {
			return null;
		}
		$m = function ( $k ) use ( $id ) {
			return get_post_meta( $id, '_gw_' . $k, true );
		};

		$gallery = array();
		foreach ( array_filter( array_map( 'absint', explode( ',', (string) $m( 'gallery' ) ) ) ) as $att ) {
			$url = wp_get_attachment_image_url( $att, 'large' );
			if ( $url ) {
				$gallery[] = array( 'url' => $url, 'alt' => get_post_meta( $att, '_wp_attachment_image_alt', true ) );
			}
		}

		$features = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $m( 'features' ) ) ) ) );
		$trans    = GoWheels_Vehicle_Meta::transmissions();
		$fuels    = GoWheels_Vehicle_Meta::fuels();

		$excerpt = '';
		if ( ! empty( $p->post_excerpt ) ) {
			$excerpt = $p->post_excerpt;
		} elseif ( ! empty( $p->post_content ) ) {
			$excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $p->post_content ) ), 35, '...' );
		}

		return array(
			'id'              => (int) $id,
			'title'           => get_the_title( $id ),
			'excerpt'         => $excerpt,
			'content'         => '',
			'permalink'       => get_permalink( $id ),
			'image'           => get_the_post_thumbnail_url( $id, 'large' ) ? get_the_post_thumbnail_url( $id, 'large' ) : '',
			'gallery'         => $gallery,
			'price_daily'     => (float) $m( 'price_daily' ),
			'price_hourly'    => (float) $m( 'price_hourly' ),
			'min_rental'      => max( 1, (int) $m( 'min_rental' ) ),
			'transmission'    => isset( $trans[ $m( 'transmission' ) ] ) ? $trans[ $m( 'transmission' ) ] : '',
			'fuel_type'       => isset( $fuels[ $m( 'fuel_type' ) ] ) ? $fuels[ $m( 'fuel_type' ) ] : '',
			'seats'           => (int) $m( 'seats' ),
			'luggage'         => (int) $m( 'luggage' ),
			'location_id'     => (int) $m( 'location_id' ),
			'location_name'   => GoWheels_Util::location_name( (int) $m( 'location_id' ) ),
			'features'        => $features,
		);
	}

	public static function ids( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'post_type'   => GoWheels_Vehicle_CPT::TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
				'fields'      => 'ids',
			)
		);
		return array_map( 'intval', get_posts( $args ) );
	}

	public static function all( $limit = -1 ) {
		$out = array();
		foreach ( self::ids( array( 'numberposts' => $limit ) ) as $id ) {
			$d = self::data( $id );
			if ( $d ) {
				$out[] = $d;
			}
		}
		return $out;
	}

	/** Blocked (maintenance) ranges as [start_utc, end_utc] pairs; end is exclusive. */
	public static function blocked_ranges( $id ) {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $id, '_gw_blocked_ranges', true ) ) as $line ) {
			if ( ! preg_match( '/^(\d{4}-\d{2}-\d{2}) to (\d{4}-\d{2}-\d{2})$/', trim( $line ), $m ) ) {
				continue;
			}
			$start = GoWheels_Util::to_utc( $m[1] . ' 00:00' );
			$end   = GoWheels_Util::to_utc( gmdate( 'Y-m-d', strtotime( $m[2] . ' +1 day' ) ) . ' 00:00' );
			if ( $start && $end ) {
				$out[] = array( $start, $end );
			}
		}
		return $out;
	}
}
