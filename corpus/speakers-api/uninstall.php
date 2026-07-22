<?php
/**
 * Uninstall cleanup for Speakers API.
 *
 * @package Spkr
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all speakers and their meta.
$speakers = get_posts(
	array(
		'post_type'      => 'spkr_speaker',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $speakers as $speaker_id ) {
	// Delete post meta (including 'role' meta field).
	delete_post_meta( $speaker_id, 'role' );

	// Force delete the post (bypass trash).
	wp_delete_post( $speaker_id, true );
}
