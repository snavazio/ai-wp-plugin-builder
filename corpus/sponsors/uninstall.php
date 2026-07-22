<?php
/**
 * Uninstall cleanup for Sponsors.
 *
 * @package Spns
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all sponsor posts.
$spns_posts = get_posts(
	array(
		'post_type'      => 'spns_sponsor',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $spns_posts as $spns_post_id ) {
	wp_delete_post( $spns_post_id, true );
}
