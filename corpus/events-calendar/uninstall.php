<?php
/**
 * Uninstall cleanup for Events Calendar.
 *
 * @package Evcal
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

// Delete all event meta.
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE %s", $wpdb->esc_like( 'evcal_' ) . '%' ) );
