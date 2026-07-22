<?php
/**
 * AJAX handlers for star rating submission.
 *
 * @package Arsr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX star rating submission.
 *
 * @return void
 */
function arsr_ajax_submit_rating() {
	check_ajax_referer( 'arsr_star_rating_nonce', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$rating  = isset( $_POST['rating'] ) ? absint( wp_unslash( $_POST['rating'] ) ) : 0;

	if ( ! $post_id || $rating < 1 || $rating > 5 ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid rating data.', 'ajax-star-rating' ) ) );
	}

	$total = get_post_meta( $post_id, 'arsr_total', true );
	$count = get_post_meta( $post_id, 'arsr_count', true );

	if ( ! is_numeric( $total ) ) {
		$total = 0;
	}
	if ( ! is_numeric( $count ) ) {
		$count = 0;
	}

	$total += $rating;
	++$count;
	$average = $count ? $total / $count : 0;

	update_post_meta( $post_id, 'arsr_total', $total );
	update_post_meta( $post_id, 'arsr_count', $count );

	wp_send_json_success( array( 'average' => $average ) );
}
add_action( 'wp_ajax_star_rating_submit', 'arsr_ajax_submit_rating' );
add_action( 'wp_ajax_nopriv_star_rating_submit', 'arsr_ajax_submit_rating' );
