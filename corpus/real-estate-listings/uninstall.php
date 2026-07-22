<?php
/**
 * Uninstall cleanup for Real Estate Listings.
 *
 * @package Reli
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all property posts.
$properties = get_posts(
	array(
		'post_type'      => 'property',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $properties as $property_id ) {
	wp_delete_post( $property_id, true );
}
