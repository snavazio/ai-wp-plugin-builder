<?php
/**
 * AJAX handlers for Availability Check functionality.
 *
 * @package Avch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX availability check request.
 *
 * @return void
 */
function avch_ajax_availability_check() {
	check_ajax_referer( 'avch_availability_nonce', 'nonce' );

	$date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';

	// Get the booked dates from the option.
	$booked_dates = get_option( 'avch_booked_dates', array() );

	// Check if the date is in the booked dates.
	$is_booked = in_array( $date, $booked_dates, true );

	// Return the result.
	if ( $is_booked ) {
		wp_send_json_error( array( 'message' => esc_html__( 'This date is booked.', 'ajax-availability-check' ) ) );
	} else {
		wp_send_json_success( array( 'message' => esc_html__( 'This date is available.', 'ajax-availability-check' ) ) );
	}
}
add_action( 'wp_ajax_availability_check', 'avch_ajax_availability_check' );
add_action( 'wp_ajax_nopriv_availability_check', 'avch_ajax_availability_check' );
