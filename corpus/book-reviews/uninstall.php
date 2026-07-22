<?php
/**
 * Uninstall cleanup for Book Reviews.
 *
 * @package Brpl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all book reviews and their metadata.
$reviews = get_posts(
	array(
		'post_type'      => 'book_review',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $reviews as $review_id ) {
	// Delete post meta.
	delete_post_meta( $review_id, 'brpl_author' );
	delete_post_meta( $review_id, 'brpl_rating' );

	// Force delete the post (bypass trash).
	wp_delete_post( $review_id, true );
}
