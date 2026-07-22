<?php
/**
 * Uninstall cleanup for Business Directory Pro.
 *
 * @package Bdp
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all listings (CPT) and their associated meta.
$bdp_posts = get_posts(
	array(
		'post_type'      => 'bdp_listing',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $bdp_posts as $bdp_post_id ) {
	wp_delete_post( $bdp_post_id, true );
}
