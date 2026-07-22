<?php
/**
 * Uninstall cleanup for Quotes Collection.
 *
 * @package Qcl1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all quote posts.
$quotes = get_posts(
	array(
		'post_type'      => 'quote',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $quotes as $quote_id ) {
	wp_delete_post( $quote_id, true );
}
