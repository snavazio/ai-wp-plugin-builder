<?php
/**
 * Uninstall cleanup for Vehicles.
 *
 * @package Vclt
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all vehicle posts.
$vehicles = get_posts(
	array(
		'post_type'      => 'vehicle',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $vehicles as $vehicle_id ) {
	wp_delete_post( $vehicle_id, true );
}
