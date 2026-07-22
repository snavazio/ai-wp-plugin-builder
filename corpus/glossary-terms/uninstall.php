<?php
/**
 * Uninstall cleanup for Glossary Terms.
 *
 * @package Gterm
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all glossary terms.
$terms = get_posts(
	array(
		'post_type'      => 'gterm_glossary_term',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $terms as $term_id ) {
	wp_delete_post( $term_id, true );
}
