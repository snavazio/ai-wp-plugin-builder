<?php
/**
 * REST API endpoint for Stores.
 *
 * @package Strs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for published stores.
 */
class Strs_REST_API {

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
			'strs/v1',
			'/stores',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_stores' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published stores.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_stores( $request ) {
		$args = array(
			'post_type'      => 'strs_store',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$stores = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$address = get_post_meta( $post_id, 'strs_address', true );
			$hours   = get_post_meta( $post_id, 'strs_hours', true );

			$stores[] = array(
				'id'      => $post_id,
				'title'   => esc_html( get_the_title() ),
				'address' => $address ? esc_html( $address ) : '',
				'hours'   => $hours ? esc_html( $hours ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $stores, 200 );
	}
}
