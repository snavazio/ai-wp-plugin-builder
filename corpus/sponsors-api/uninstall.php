<?php
/**
 * Uninstall cleanup for Sponsors API.
 *
 * @package Spns
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all sponsors and their meta.
$sponsors = get_posts(
	array(
		'post_type'      => 'spns_sponsor',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $sponsors as $sponsor_id ) {
	// Delete post meta.
	delete_post_meta( $sponsor_id, 'spns_website' );

	// Force delete the post (bypass trash).
	wp_delete_post( $sponsor_id, true );
}
