<?php
/**
 * AJAX handlers for RSVP form submission.
 *
 * @package Ersv
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX RSVP form submission.
 *
 * @return void
 */
function ersv_ajax_rsvp() {
	check_ajax_referer( 'ersv_rsvp_nonce', '_wpnonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to RSVP.', 'event-rsvp' ) ) );
	}

	$event_id = isset( $_POST['event_id'] ) ? absint( wp_unslash( $_POST['event_id'] ) ) : 0;
	if ( $event_id <= 0 || get_post_type( $event_id ) !== 'event' ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid event.', 'event-rsvp' ) ) );
	}

	$count = (int) get_post_meta( $event_id, '_ersv_rsvp_count', true );
	++$count;
	update_post_meta( $event_id, '_ersv_rsvp_count', $count );

	wp_send_json_success( array( 'message' => esc_html__( 'Thank you for RSVPing!', 'event-rsvp' ) ) );
}
add_action( 'wp_ajax_ersv_rsvp', 'ersv_ajax_rsvp' );
add_action( 'wp_ajax_nopriv_ersv_rsvp', 'ersv_ajax_rsvp' );
