<?php
/**
 * Uninstall cleanup for Services API.
 *
 * @package Srvapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all services and their meta.
$services = get_posts(
	array(
		'post_type'      => 'srvapi_service',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $services as $service_id ) {
	// Delete post meta.
	delete_post_meta( $service_id, 'srvapi_price' );

	// Force delete the post (bypass trash).
	wp_delete_post( $service_id, true );
}
