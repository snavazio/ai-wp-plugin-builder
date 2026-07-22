<?php
/**
 * AJAX handlers for Reaction Buttons functionality.
 *
 * @package Reac
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX reaction click request.
 *
 * @return void
 */
function reac_ajax_reaction_click() {
	// Verify nonce for both logged-in and logged-out users.
	check_ajax_referer( 'reac_reaction_nonce', 'nonce' );

	// Check capability for logged-in users.
	if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'reaction-buttons' ), 403 );
	}

	// Sanitize input.
	$post_id  = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$reaction = isset( $_POST['reaction'] ) ? sanitize_text_field( wp_unslash( $_POST['reaction'] ) ) : '';

	if ( ! $post_id || empty( $reaction ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid request data.', 'reaction-buttons' ) ) );
	}

	// Store reaction in post meta.
	update_post_meta( $post_id, 'reac_reaction', $reaction );

	wp_send_json_success( array( 'message' => esc_html__( 'Reaction recorded successfully.', 'reaction-buttons' ) ) );
}
add_action( 'wp_ajax_reac_reaction_click', 'reac_ajax_reaction_click' );
add_action( 'wp_ajax_nopriv_reac_reaction_click', 'reac_ajax_reaction_click' );
