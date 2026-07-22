<?php
/**
 * Uninstall cleanup for Plant Guide.
 *
 * @package Pgui
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all plant posts and their meta.
$plant_posts = get_posts(
	array(
		'post_type'        => 'pgui_plant',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $plant_posts as $plant_post_id ) {
	wp_delete_post( $plant_post_id, true );
}
