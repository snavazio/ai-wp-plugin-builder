<?php
/**
 * AJAX handlers for comment voting.
 *
 * @package Acv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX comment vote submission.
 *
 * @return void
 */
function acv1_ajax_vote() {
	check_ajax_referer( 'acv_vote_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to vote.', 'ajax-comment-vote' ) ) );
	}

	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;

	if ( ! $comment_id || ! get_comment( $comment_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid comment ID.', 'ajax-comment-vote' ) ) );
	}

	$current_count = (int) get_comment_meta( $comment_id, '_acv1_vote_count', true );
	$new_count     = $current_count + 1;

	update_comment_meta( $comment_id, '_acv1_vote_count', $new_count );

	wp_send_json_success( array( 'count' => $new_count ) );
}
add_action( 'wp_ajax_acv1_vote', 'acv1_ajax_vote' );
add_action( 'wp_ajax_nopriv_acv1_vote', 'acv1_ajax_vote' );
