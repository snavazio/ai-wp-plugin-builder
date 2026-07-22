<?php
/**
 * Uninstall cleanup for Stores API.
 *
 * @package Strs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all stores and their meta.
$stores = get_posts(
	array(
		'post_type'      => 'strs_store',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $stores as $store_id ) {
	// Delete post meta.
	delete_post_meta( $store_id, 'strs_address' );
	delete_post_meta( $store_id, 'strs_hours' );

	// Force delete the post (bypass trash).
	wp_delete_post( $store_id, true );
}
