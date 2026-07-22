<?php
/**
 * REST API endpoint for Artworks.
 *
 * @package Artw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for artworks.
 */
class Artw_REST_API {

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
			'artworks-api/v1',
			'/artworks',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_artworks' ),
				'permission_callback' => array( __CLASS__, 'check_permission' ),
			)
		);
	}

	/**
	 * Check if the current user can access the endpoint.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return bool|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public static function check_permission( $request ) {
		if ( ! current_user_can( 'read' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission to access this endpoint.', 'artworks-api' ), array( 'status' => 403 ) );
		}
		return true;
	}

	/**
	 * Get published artworks.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_artworks( $request ) {
		$args = array(
			'post_type'      => 'artw_artwork',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$artworks = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$artist = get_post_meta( $post_id, 'artw_artist', true );

			$artworks[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'artist'    => $artist ? esc_html( $artist ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $artworks, 200 );
	}
}
