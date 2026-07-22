<?php
/**
 * Uninstall cleanup for Job Listings.
 *
 * @package Jobl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all job posts.
$jobs = get_posts(
	array(
		'post_type'      => 'job',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $jobs as $job_id ) {
	wp_delete_post( $job_id, true );
}
