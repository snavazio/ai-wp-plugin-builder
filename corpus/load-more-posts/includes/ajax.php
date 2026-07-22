<?php
/**
 * AJAX handlers for load more posts functionality.
 *
 * @package Lmpa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX load-more request.
 *
 * @return void
 */
function lmpa_ajax_load_more() {
	check_ajax_referer( 'lmpa_load_more_nonce', 'nonce' );

	$page  = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;
	$count = isset( $_POST['count'] ) ? absint( wp_unslash( $_POST['count'] ) ) : 9;

	if ( $page < 1 ) {
		$page = 1;
	}
	if ( $count < 1 ) {
		$count = 9;
	}

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		wp_send_json_error( array( 'message' => __( 'No more posts found.', 'load-more-posts' ) ) );
	}

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		lmpa_render_post_item();
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
add_action( 'wp_ajax_lmpa_load_more', 'lmpa_ajax_load_more' );
add_action( 'wp_ajax_nopriv_lmpa_load_more', 'lmpa_ajax_load_more' );
