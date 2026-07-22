<?php
/**
 * Uninstall cleanup for Quotes API.
 *
 * @package Qapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all quotes and their meta.
$quotes = get_posts(
	array(
		'post_type'      => 'qapi_quote',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $quotes as $quote_id ) {
	// Delete post meta.
	delete_post_meta( $quote_id, 'qapi_author' );

	// Force delete the post (bypass trash).
	wp_delete_post( $quote_id, true );
}
