<?php
/**
 * Uninstall cleanup for Team API.
 *
 * @package Tapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all team members and their meta.
$members = get_posts(
	array(
		'post_type'      => 'tapi_member',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $members as $member_id ) {
	// Delete post meta.
	delete_post_meta( $member_id, 'tapi_role' );

	// Force delete the post (bypass trash).
	wp_delete_post( $member_id, true );
}
