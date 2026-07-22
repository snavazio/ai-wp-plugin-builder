<?php
/**
 * Uninstall cleanup for FAQs.
 *
 * @package Faqs
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all FAQ posts.
$faqs = get_posts(
	array(
		'post_type'      => 'faqs_faq',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $faqs as $faq_id ) {
	wp_delete_post( $faq_id, true );
}
