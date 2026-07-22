<?php
/**
 * Uninstall cleanup for Speakers.
 *
 * @package Spkrs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all speaker posts.
$spkrs_posts = get_posts(
	array(
		'post_type'      => 'speaker',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $spkrs_posts as $spkrs_post_id ) {
	wp_delete_post( $spkrs_post_id, true );
}

// Delete all meta for speaker posts.
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = %s)", 'speaker' ) );
