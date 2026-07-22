<?php
/**
 * REST API endpoint for Services.
 *
 * @package Srvapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for services.
 */
class Srvapi_REST_API {

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
			'services-api/v1',
			'/services',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_services' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published services.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_services( $request ) {
		$args = array(
			'post_type'      => 'srvapi_service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$services = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$price = get_post_meta( $post_id, 'srvapi_price', true );

			$services[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'price'     => $price ? esc_html( $price ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $services, 200 );
	}
}
