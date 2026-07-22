<?php
/**
 * Uninstall cleanup for Book Library.
 *
 * @package Blib
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all book posts and their meta.
$book_posts = get_posts(
	array(
		'post_type'        => 'blib_book',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $book_posts as $book_post_id ) {
	wp_delete_post( $book_post_id, true );
}
