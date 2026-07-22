<?php
/**
 * Uninstall cleanup for Testimonials API.
 *
 * @package Tstm
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all testimonials and their meta.
$testimonials = get_posts(
	array(
		'post_type'      => 'tstm_testimonial',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $testimonials as $testimonial_id ) {
	// Delete post meta.
	delete_post_meta( $testimonial_id, 'tstm_author' );

	// Force delete the post (bypass trash).
	wp_delete_post( $testimonial_id, true );
}
