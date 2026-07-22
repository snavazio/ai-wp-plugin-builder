<?php
/**
 * Uninstall cleanup for Workshop CPT Manager.
 *
 * @package Wcpm
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all workshop posts and their meta.
$workshops = get_posts(
	array(
		'post_type'      => 'workshop',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $workshops as $workshop_id ) {
	// Delete meta for this workshop.
	delete_post_meta( $workshop_id, 'wcpm_date' );
	delete_post_meta( $workshop_id, 'wcpm_seats' );
	// Delete the post.
	wp_delete_post( $workshop_id, true );
}
