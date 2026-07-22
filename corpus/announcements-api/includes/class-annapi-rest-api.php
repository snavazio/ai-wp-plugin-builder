<?php
/**
 * REST API endpoint for Announcements.
 *
 * @package Annapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for published announcements.
 */
class Annapi_REST_API {

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
			'announcements-api/v1',
			'/announcements',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_announcements' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published announcements.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_announcements( $request ) {
		$args = array(
			'post_type'      => 'annapi_announcement',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$announcements = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$announcements[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'excerpt'   => esc_html( get_the_excerpt() ),
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $announcements, 200 );
	}
}
