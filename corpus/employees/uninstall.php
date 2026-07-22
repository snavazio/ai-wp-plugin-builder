<?php
/**
 * Uninstall cleanup for Employees.
 *
 * @package Empc
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all employee posts.
$empc_posts = get_posts(
	array(
		'post_type'      => 'employee',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $empc_posts as $empc_post_id ) {
	wp_delete_post( $empc_post_id, true );
}
