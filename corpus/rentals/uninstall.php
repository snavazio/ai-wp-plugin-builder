<?php
/**
 * Uninstall cleanup for Rentals.
 *
 * @package Rent
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all rental posts.
$rentals = get_posts(
	array(
		'post_type'      => 'rental',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $rentals as $rental_id ) {
	wp_delete_post( $rental_id, true );
}

// Delete all rental meta.
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE %s", $wpdb->esc_like( 'rent_' ) . '%' ) );
