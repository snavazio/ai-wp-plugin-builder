<?php
/**
 * Uninstall cleanup for Announcements API.
 *
 * @package Annapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all announcements and their meta.
$announcements = get_posts(
	array(
		'post_type'      => 'annapi_announcement',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $announcements as $announcement_id ) {
	// Delete post meta.
	delete_post_meta( $announcement_id, '_thumbnail_id' );
	delete_post_meta( $announcement_id, 'excerpt' );

	// Force delete the post (bypass trash).
	wp_delete_post( $announcement_id, true );
}
