<?php
/**
 * Uninstall cleanup for FAQ API.
 *
 * @package Faqapi
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all FAQs and their meta.
$faqs = get_posts(
	array(
		'post_type'      => 'faqapi_faq',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $faqs as $faq_id ) {
	// Delete post meta (none defined, but safe to call).
	delete_post_meta( $faq_id, '_thumbnail_id' );

	// Force delete the post (bypass trash).
	wp_delete_post( $faq_id, true );
}
