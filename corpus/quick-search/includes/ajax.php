<?php
/**
 * AJAX handlers for Quick Search suggestions.
 *
 * @package Qsrch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX request for title suggestions.
 *
 * @return void
 */
function qsrch_ajax_suggestions() {
	check_ajax_referer( 'quick_search_nonce', 'nonce' );

	$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';

	if ( empty( $query ) ) {
		wp_send_json_success( array() );
	}

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		's'              => $query,
	);

	$query = new WP_Query( $args );

	$suggestions = array();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$suggestions[] = esc_html( get_the_title() );
		}
		wp_reset_postdata();
	}

	wp_send_json_success( $suggestions );
}
add_action( 'wp_ajax_quick_search_suggestions', 'qsrch_ajax_suggestions' );
add_action( 'wp_ajax_nopriv_quick_search_suggestions', 'qsrch_ajax_suggestions' );
