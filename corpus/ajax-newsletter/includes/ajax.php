<?php
/**
 * AJAX handlers for AJAX Newsletter functionality.
 *
 * @package Anews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX newsletter submission.
 *
 * @return void
 */
function anews_ajax_submit() {
	check_ajax_referer( 'anews_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( empty( $email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Email is required.', 'ajax-newsletter' ) ) );
	}

	$emails = get_option( 'anews_emails', array() );
	if ( in_array( $email, $emails, true ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'This email is already subscribed.', 'ajax-newsletter' ) ) );
	}

	$emails[] = $email;
	update_option( 'anews_emails', $emails );

	wp_send_json_success( array( 'message' => esc_html__( 'Thank you for subscribing!', 'ajax-newsletter' ) ) );
}
add_action( 'wp_ajax_anews_submit', 'anews_ajax_submit' );
add_action( 'wp_ajax_nopriv_anews_submit', 'anews_ajax_submit' );
