<?php
/**
 * Register REST API endpoints for Help Tickets.
 *
 * @package Help
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the help_tickets REST endpoint.
 *
 * @return void
 */
function help_register_rest_endpoints() {
	register_rest_route(
		'help/v1',
		'/tickets',
		array(
			'methods'             => 'GET',
			'callback'            => 'help_get_tickets',
			'permission_callback' => 'help_rest_permission_callback',
			'args'                => array(
				'status' => array(
					'description'       => __( 'Filter by status slug', 'help-desk' ),
					'type'              => 'string',
					'validate_callback' => 'rest_validate_request_arg',
				),
				'count'  => array(
					'description'       => __( 'Number of tickets to return', 'help-desk' ),
					'type'              => 'integer',
					'default'           => 10,
					'validate_callback' => 'rest_validate_request_arg',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'help_register_rest_endpoints' );

/**
 * Get help tickets.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response The response object.
 */
function help_get_tickets( $request ) {
	$status = $request->get_param( 'status' );
	$count  = $request->get_param( 'count' );

	// Sanitize and validate.
	$status = sanitize_key( $status );
	$count  = absint( $count );
	if ( $count < 1 ) {
		$count = 10;
	}

	$args = array(
		'post_type'      => 'help_ticket',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $status ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'help_status',
				'field'    => 'slug',
				'terms'    => $status,
			),
		);
	}

	$query = new WP_Query( $args );

	$posts = array();
	foreach ( $query->posts as $post ) {
		$posts[] = array(
			'id'        => $post->ID,
			'title'     => get_the_title( $post->ID ),
			'content'   => get_the_content( null, false, $post->ID ),
			'status'    => get_the_term_list( $post->ID, 'help_status', '', ', ', '' ),
			'permalink' => get_permalink( $post->ID ),
			'date'      => $post->post_date,
		);
	}

	return new WP_REST_Response( $posts, 200 );
}

/**
 * Permission callback for the REST endpoint.
 *
 * @param WP_REST_Request $request The request object.
 * @return bool|WP_Error True if the user can access, WP_Error otherwise.
 */
function help_rest_permission_callback( $request ) {
	if ( ! current_user_can( 'read' ) ) {
		return new WP_Error( 'rest_forbidden', __( 'You do not have permission to access this resource.', 'help-desk' ), array( 'status' => 403 ) );
	}
	return true;
}
