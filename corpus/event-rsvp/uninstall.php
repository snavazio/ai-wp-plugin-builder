<?php
/**
 * Uninstall cleanup for Event RSVP.
 *
 * @package Ersv
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all event posts.
$events = get_posts(
	array(
		'post_type'      => 'event',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $events as $event_id ) {
	wp_delete_post( $event_id, true );
}
