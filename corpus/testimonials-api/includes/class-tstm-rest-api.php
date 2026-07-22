<?php
/**
 * REST API endpoint for Testimonials.
 *
 * @package Tstm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for published testimonials.
 */
class Tstm_REST_API {

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
			'tstm/v1',
			'/testimonials',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_testimonials' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published testimonials.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_testimonials( $request ) {
		$args = array(
			'post_type'      => 'tstm_testimonial',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$testimonials = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$author = get_post_meta( $post_id, 'tstm_author', true );

			$testimonials[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'author'    => $author ? esc_html( $author ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $testimonials, 200 );
	}
}
