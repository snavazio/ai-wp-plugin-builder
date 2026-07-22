<?php
/**
 * REST API endpoint for Team Members.
 *
 * @package Tapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for team members.
 */
class Tapi_REST_API {

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
			'team-api/v1',
			'/members',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_members' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published team members.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_members( $request ) {
		$args = array(
			'post_type'      => 'tapi_member',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$members = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$role = get_post_meta( $post_id, 'tapi_role', true );

			$members[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'role'      => $role ? esc_html( $role ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $members, 200 );
	}
}
