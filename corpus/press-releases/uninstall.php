<?php
/**
 * Uninstall cleanup for Press Releases.
 *
 * @package Prcs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all press release posts.
$press_releases = get_posts(
	array(
		'post_type'      => 'press_release',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $press_releases as $press_release_id ) {
	wp_delete_post( $press_release_id, true );
}

// Delete all press release meta.
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE %s", $wpdb->esc_like( 'prcs_release_date' ) . '%' ) );
