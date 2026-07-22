<?php
/**
 * AJAX handlers for AJAX Post Filter functionality.
 *
 * @package Apf1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX post filter request.
 *
 * @return void
 */
function apf1_ajax_post_filter() {
	check_ajax_referer( 'apf1_post_filter_nonce', 'nonce' );

	$category = isset( $_POST['category'] ) ? sanitize_key( wp_unslash( $_POST['category'] ) ) : '';

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['category_name'] = $category;
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'No posts found for this category.', 'ajax-post-filter' ) ) );
	}

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		apf1_render_post_item();
	}
	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_post_filter', 'apf1_ajax_post_filter' );
add_action( 'wp_ajax_nopriv_post_filter', 'apf1_ajax_post_filter' );
