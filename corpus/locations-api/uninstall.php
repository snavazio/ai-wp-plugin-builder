<?php
/**
 * Uninstall cleanup for Locations API.
 *
 * @package Locapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all locations and their meta.
$locations = get_posts(
	array(
		'post_type'      => 'locapi_location',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $locations as $location_id ) {
	// Delete post meta.
	delete_post_meta( $location_id, 'locapi_latitude' );
	delete_post_meta( $location_id, 'locapi_longitude' );

	// Force delete the post (bypass trash).
	wp_delete_post( $location_id, true );
}
