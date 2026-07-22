<?php
/**
 * AJAX handlers for contact form submission.
 *
 * @package Acff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX contact form submission.
 *
 * @return void
 */
function acff_ajax_contact_form_submit() {
	check_ajax_referer( 'contact_form_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to submit this form.', 'ajax-contact-form' ) ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';

	if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'All fields are required.', 'ajax-contact-form' ) ) );
	}

	$insert_result = wp_insert_post(
		array(
			'post_title'   => $name,
			'post_content' => $message,
			'post_type'    => 'acff_submission',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
		)
	);

	/**
	 * The result of the post insertion.
	 *
	 * @var int|WP_Error $insert_result
	 */
	if ( is_wp_error( $insert_result ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Failed to save your submission.', 'ajax-contact-form' ) ) );
	}

	$post_id = $insert_result;

	update_post_meta( $post_id, '_acff_email', $email );

	wp_send_json_success( array( 'message' => esc_html__( 'Thank you for your submission!', 'ajax-contact-form' ) ) );
}
add_action( 'wp_ajax_contact_form_submit', 'acff_ajax_contact_form_submit' );
add_action( 'wp_ajax_nopriv_contact_form_submit', 'acff_ajax_contact_form_submit' );
