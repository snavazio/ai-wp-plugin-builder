<?php
/**
 * Uninstall cleanup for Testimonials.
 *
 * @package Tmnl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all testimonial posts and their meta data.
$testimonials = get_posts(
	array(
		'post_type'      => 'tmnl_testimonial',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	)
);

foreach ( $testimonials as $testimonial_id ) {
	// Delete post meta.
	delete_post_meta( $testimonial_id, 'tmnl_rating' );
	delete_post_meta( $testimonial_id, 'tmnl_customer_name' );
	delete_post_meta( $testimonial_id, 'tmnl_customer_company' );

	// Force delete the post (bypass trash).
	wp_delete_post( $testimonial_id, true );
}
