<?php
/**
 * AJAX handlers for AJAX Tabs functionality.
 *
 * @package Ajxt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX tabs load request.
 *
 * @return void
 */
function ajxt_ajax_tabs_load() {
	check_ajax_referer( 'ajxt_tabs_nonce', 'nonce' );

	$tab = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $tab ) ) {
		$args['category_name'] = $tab;
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'No posts found for this tab.', 'ajax-tabs' ) ) );
	}

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		ajxt_render_post_item();
	}
	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_ajax_tabs_load', 'ajxt_ajax_tabs_load' );
add_action( 'wp_ajax_nopriv_ajax_tabs_load', 'ajxt_ajax_tabs_load' );
