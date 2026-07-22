<?php
/**
 * Uninstall cleanup for Portfolio Skills.
 *
 * @package Pskl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all work posts.
$works = get_posts(
	array(
		'post_type'      => 'pskl_work',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $works as $work_id ) {
	wp_delete_post( $work_id, true );
}

// Delete skill taxonomy terms.
$terms = get_terms(
	array(
		'taxonomy'   => 'pskl_skill',
		'hide_empty' => false,
		'fields'     => 'ids',
	)
);

if ( ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term_id ) {
		wp_delete_term( $term_id, 'pskl_skill' );
	}
}
