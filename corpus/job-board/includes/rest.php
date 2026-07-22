<?php
/**
 * Register REST API endpoints for Jobs.
 *
 * @package Jbrd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the jbrd_jobs REST endpoint.
 *
 * @return void
 */
function jbrd_register_rest_endpoints() {
	register_rest_route(
		'jbrd/v1',
		'/jobs',
		array(
			'methods'             => 'GET',
			'callback'            => 'jbrd_get_jobs',
			'permission_callback' => 'jbrd_rest_permission_callback',
			'args'                => array(
				'category' => array(
					'description'       => __( 'Filter by category slug', 'job-board' ),
					'type'              => 'string',
					'validate_callback' => 'rest_validate_request_arg',
				),
				'count'    => array(
					'description'       => __( 'Number of jobs to return', 'job-board' ),
					'type'              => 'integer',
					'default'           => 10,
					'validate_callback' => 'rest_validate_request_arg',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'jbrd_register_rest_endpoints' );

/**
 * Get job listings.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response The response object.
 */
function jbrd_get_jobs( $request ) {
	$category = $request->get_param( 'category' );
	$count    = $request->get_param( 'count' );

	// Sanitize and validate.
	$category = sanitize_key( $category );
	$count    = absint( $count );
	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'jbrd_job',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'jbrd_job_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	$jobs = array();
	foreach ( $query->posts as $post ) {
		$jobs[] = array(
			'id'         => $post->ID,
			'title'      => get_the_title( $post->ID ),
			'location'   => get_post_meta( $post->ID, 'jbrd_location', true ),
			'salary'     => get_post_meta( $post->ID, 'jbrd_salary', true ),
			'categories' => get_the_term_list( $post->ID, 'jbrd_job_category', '', ', ', '' ),
			'permalink'  => get_permalink( $post->ID ),
			'date'       => $post->post_date,
		);
	}

	return new WP_REST_Response( $jobs, 200 );
}

/**
 * Permission callback for the REST endpoint.
 *
 * @param WP_REST_Request $request The request object.
 * @return bool|WP_Error True if the user can access, WP_Error otherwise.
 */
function jbrd_rest_permission_callback( $request ) {
	if ( ! current_user_can( 'read' ) ) {
		return new WP_Error( 'rest_forbidden', __( 'You do not have permission to access this resource.', 'job-board' ), array( 'status' => 403 ) );
	}
	return true;
}
