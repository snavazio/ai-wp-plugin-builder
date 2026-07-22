<?php
/**
 * Uninstall cleanup for Downloads.
 *
 * @package Dlm1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all download posts.
$downloads = get_posts(
	array(
		'post_type'      => 'download',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $downloads as $download_id ) {
	wp_delete_post( $download_id, true );
}

// Delete all download meta.
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE %s", $wpdb->esc_like( 'dlm1_' ) . '%' ) );
