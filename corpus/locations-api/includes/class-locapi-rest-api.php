<?php
/**
 * REST API endpoint for Locations.
 *
 * @package Locapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for published locations.
 */
class Locapi_REST_API {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			'locapi/v1',
			'/locations',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_locations' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published locations.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_locations( $request ) {
		$args = array(
			'post_type'      => 'locapi_location',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$locations = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$latitude  = get_post_meta( $post_id, 'locapi_latitude', true );
			$longitude = get_post_meta( $post_id, 'locapi_longitude', true );

			$locations[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'latitude'  => $latitude ? esc_html( $latitude ) : '',
				'longitude' => $longitude ? esc_html( $longitude ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $locations, 200 );
	}
}
