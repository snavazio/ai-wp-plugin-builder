<?php
/**
 * Uninstall cleanup for AJAX Contact Form.
 *
 * @package Acff
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all contact submission posts.
$acff_posts = get_posts(
	array(
		'post_type'      => 'acff_submission',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $acff_posts as $acff_post_id ) {
	wp_delete_post( $acff_post_id, true );
}
