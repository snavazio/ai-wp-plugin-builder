<?php
/**
 * AJAX handlers for Vote Poll functionality.
 *
 * @package Vtpol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX vote request.
 *
 * @return void
 */
function vtpol_ajax_vote_poll() {
	check_ajax_referer( 'vtpol_vote_nonce', 'nonce' );

	// Check capability for logged-in users.
	if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to vote.', 'vote-poll' ), 403 );
	}

	// Sanitize input.
	$poll_id = isset( $_POST['poll_id'] ) ? sanitize_key( wp_unslash( $_POST['poll_id'] ) ) : '';
	$vote    = isset( $_POST['vote'] ) ? sanitize_text_field( wp_unslash( $_POST['vote'] ) ) : '';

	// Validate input.
	if ( empty( $poll_id ) || ! in_array( $vote, array( 'yes', 'no' ), true ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid vote data.', 'vote-poll' ) ) );
	}

	// Get current counts.
	$counts = get_option(
		$poll_id,
		array(
			'yes' => 0,
			'no'  => 0,
		)
	);

	// Update counts.
	++$counts[ $vote ];
	update_option( $poll_id, $counts );

	// Return updated counts.
	wp_send_json_success(
		array(
			'yes' => $counts['yes'],
			'no'  => $counts['no'],
		)
	);
}
add_action( 'wp_ajax_vtpol_vote_poll', 'vtpol_ajax_vote_poll' );
add_action( 'wp_ajax_nopriv_vtpol_vote_poll', 'vtpol_ajax_vote_poll' );
