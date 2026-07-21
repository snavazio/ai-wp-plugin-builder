<?php
/**
 * AJAX handlers for portfolio load-more functionality.
 *
 * @package Prtf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX load-more request.
 *
 * @return void
 */
function prtf_ajax_load_more() {
	check_ajax_referer( 'prtf_load_more_nonce', 'nonce' );

	$page  = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;
	$count = isset( $_POST['count'] ) ? absint( wp_unslash( $_POST['count'] ) ) : 9;
	$type  = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';

	if ( $page < 1 ) {
		$page = 1;
	}
	if ( $count < 1 ) {
		$count = 9;
	}

	$args = array(
		'post_type'      => 'prtf_portfolio',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $type ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'prtf_project_type',
				'field'    => 'slug',
				'terms'    => $type,
			),
		);
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		wp_send_json_error( array( 'message' => __( 'No more posts found.', 'portfolio' ) ) );
	}

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		prtf_render_portfolio_item();
	}
	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'      => $html,
			'max_pages' => $query->max_num_pages,
		)
	);
}
add_action( 'wp_ajax_prtf_load_more', 'prtf_ajax_load_more' );
add_action( 'wp_ajax_nopriv_prtf_load_more', 'prtf_ajax_load_more' );
