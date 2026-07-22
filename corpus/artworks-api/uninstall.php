<?php
/**
 * Uninstall cleanup for Artworks API.
 *
 * @package Artw
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all artworks and their meta.
$artworks = get_posts(
	array(
		'post_type'      => 'artw_artwork',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $artworks as $artwork_id ) {
	// Delete post meta.
	delete_post_meta( $artwork_id, 'artw_artist' );

	// Force delete the post (bypass trash).
	wp_delete_post( $artwork_id, true );
}
