<?php
/**
 * Uninstall cleanup for Gallery API.
 *
 * @package Galapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all photos and their meta.
$photos = get_posts(
	array(
		'post_type'      => 'galapi_photo',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $photos as $photo_id ) {
	// Delete post meta.
	delete_post_meta( $photo_id, 'galapi_caption' );

	// Force delete the post (bypass trash).
	wp_delete_post( $photo_id, true );
}
