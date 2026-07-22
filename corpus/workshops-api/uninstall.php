<?php
/**
 * Uninstall cleanup for Workshops API.
 *
 * @package Wapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all workshops and their meta.
$workshops = get_posts(
	array(
		'post_type'      => 'wapi_workshop',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $workshops as $workshop_id ) {
	// Delete post meta.
	delete_post_meta( $workshop_id, 'workshop_date' );

	// Force delete the post (bypass trash).
	wp_delete_post( $workshop_id, true );
}
