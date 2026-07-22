<?php
/**
 * Uninstall cleanup for Staff Directory.
 *
 * @package Staff
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all staff posts.
$staff_posts = get_posts(
	array(
		'post_type'      => 'staff',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $staff_posts as $staff_post_id ) {
	wp_delete_post( $staff_post_id, true );
}
