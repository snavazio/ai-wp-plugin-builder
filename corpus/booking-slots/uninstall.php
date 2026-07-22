<?php
/**
 * Uninstall cleanup for Booking Slots.
 *
 * @package Bkslot
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all slot posts.
$slots = get_posts(
	array(
		'post_type'      => 'slot',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $slots as $slot_id ) {
	wp_delete_post( $slot_id, true );
}
