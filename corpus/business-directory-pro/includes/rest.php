<?php
/**
 * Register REST API endpoints for Business Listings.
 *
 * @package Bdp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the bdp_listings REST endpoint.
 *
 * @return void
 */
function bdp_register_rest_endpoints() {
	register_rest_route(
		'bdp/v1',
		'/listings',
		array(
			'methods'             => 'GET',
			'callback'            => 'bdp_get_listings',
			'permission_callback' => 'bdp_rest_permission_callback',
			'args'                => array(
				'category' => array(
					'description'       => __( 'Filter by category slug', 'business-directory-pro' ),
					'type'              => 'string',
					'validate_callback' => 'rest_validate_request_arg',
				),
				'region'   => array(
					'description'       => __( 'Filter by region slug', 'business-directory-pro' ),
					'type'              => 'string',
					'validate_callback' => 'rest_validate_request_arg',
				),
				'count'    => array(
					'description'       => __( 'Number of listings to return', 'business-directory-pro' ),
					'type'              => 'integer',
					'default'           => 10,
					'validate_callback' => 'rest_validate_request_arg',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'bdp_register_rest_endpoints' );

/**
 * Get business listings.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response The response object.
 */
function bdp_get_listings( $request ) {
	$category = $request->get_param( 'category' );
	$region   = $request->get_param( 'region' );
	$count    = $request->get_param( 'count' );

	// Sanitize and validate.
	$category = sanitize_key( $category );
	$region   = sanitize_key( $region );
	$count    = absint( $count );
	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'bdp_listing',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	if ( ! empty( $region ) ) {
		if ( empty( $args['tax_query'] ) ) {
			$args['tax_query'] = array();
		}
		$args['tax_query'][] = array(
			'taxonomy' => 'region',
			'field'    => 'slug',
			'terms'    => $region,
		);
	}

	$query = new WP_Query( $args );

	$listings = array();
	foreach ( $query->posts as $post ) {
		$listings[] = array(
			'id'         => $post->ID,
			'title'      => get_the_title( $post->ID ),
			'phone'      => get_post_meta( $post->ID, 'bdp_phone', true ),
			'website'    => get_post_meta( $post->ID, 'bdp_website', true ),
			'categories' => get_the_term_list( $post->ID, 'category', '', ', ', '' ),
			'regions'    => get_the_term_list( $post->ID, 'region', '', ', ', '' ),
			'permalink'  => get_permalink( $post->ID ),
			'date'       => $post->post_date,
		);
	}

	return new WP_REST_Response( $listings, 200 );
}

/**
 * Permission callback for the REST endpoint.
 *
 * @param WP_REST_Request $request The request object.
 * @return bool|WP_Error True if the user can access, WP_Error otherwise.
 */
function bdp_rest_permission_callback( $request ) {
	if ( ! current_user_can( 'read' ) ) {
		return new WP_Error( 'rest_forbidden', __( 'You do not have permission to access this resource.', 'business-directory-pro' ), array( 'status' => 403 ) );
	}
	return true;
}
