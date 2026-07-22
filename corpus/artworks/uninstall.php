<?php
/**
 * Uninstall cleanup for Artworks.
 *
 * @package Artw
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all artwork posts.
$artworks = get_posts(
	array(
		'post_type'      => 'artw_artwork',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $artworks as $artwork_id ) {
	wp_delete_post( $artwork_id, true );
}
