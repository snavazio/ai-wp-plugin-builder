<?php
/**
 * Uninstall cleanup for Services.
 *
 * @package Srvs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all service posts.
$services = get_posts(
	array(
		'post_type'      => 'service',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $services as $service_id ) {
	wp_delete_post( $service_id, true );
}
