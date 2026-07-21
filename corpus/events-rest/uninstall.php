<?php
/**
 * Uninstall cleanup for Events REST.
 *
 * @package Evtr
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all events and their metadata.
$events = get_posts(
	array(
		'post_type'      => 'evtr_event',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $events as $event_id ) {
	// Delete post meta.
	delete_post_meta( $event_id, 'evtr_event_date' );
	delete_post_meta( $event_id, 'evtr_event_location' );

	// Force delete the post (bypass trash).
	wp_delete_post( $event_id, true );
}
