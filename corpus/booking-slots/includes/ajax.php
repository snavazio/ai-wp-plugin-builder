<?php
/**
 * AJAX handlers for booking slots.
 *
 * @package Bkslot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX slot booking.
 *
 * @return void
 */
function bkslot_ajax_book_slot() {
	check_ajax_referer( 'book_slot_nonce', '_wpnonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to book slots.', 'booking-slots' ) ) );
	}

	$slot_id = isset( $_POST['slot_id'] ) ? absint( wp_unslash( $_POST['slot_id'] ) ) : 0;
	if ( $slot_id <= 0 || get_post_type( $slot_id ) !== 'slot' ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid slot.', 'booking-slots' ) ) );
	}

	// Check if slot is already booked.
	$booked = get_post_meta( $slot_id, '_bkslot_booked', true );
	if ( $booked ) {
		wp_send_json_error( array( 'message' => esc_html__( 'This slot is already booked.', 'booking-slots' ) ) );
	}

	// Mark slot as booked.
	update_post_meta( $slot_id, '_bkslot_booked', true );

	wp_send_json_success( array( 'message' => esc_html__( 'Slot booked successfully!', 'booking-slots' ) ) );
}
add_action( 'wp_ajax_book_slot', 'bkslot_ajax_book_slot' );
add_action( 'wp_ajax_nopriv_book_slot', 'bkslot_ajax_book_slot' );
