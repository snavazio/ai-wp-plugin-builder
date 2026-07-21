<?php
/**
 * REST API endpoint for Events.
 *
 * @package Evtr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the REST API endpoint for published events.
 */
class Evtr_REST_API {

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
			'events/v1',
			'/events',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_events' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get published events.
	 *
	 * @param WP_REST_Request $request Request object (unused but required by WordPress REST API signature).
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public static function get_events( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$args = array(
			'post_type'      => 'evtr_event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( array(), 200 );
		}

		$events = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$event_date     = get_post_meta( $post_id, 'evtr_event_date', true );
			$event_location = get_post_meta( $post_id, 'evtr_event_location', true );

			$events[] = array(
				'id'        => $post_id,
				'title'     => esc_html( get_the_title() ),
				'permalink' => esc_url( get_permalink() ),
				'date'      => $event_date ? esc_html( $event_date ) : '',
				'location'  => $event_location ? esc_html( $event_location ) : '',
			);
		}

		wp_reset_postdata();

		return new WP_REST_Response( $events, 200 );
	}
}
