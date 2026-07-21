<?php
/**
 * Uninstall cleanup for Portfolio.
 *
 * @package Prtf
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all portfolio posts.
$portfolios = get_posts(
	array(
		'post_type'      => 'prtf_portfolio',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $portfolios as $portfolio_id ) {
	wp_delete_post( $portfolio_id, true );
}

// Delete project type taxonomy terms.
$terms = get_terms(
	array(
		'taxonomy'   => 'prtf_project_type',
		'hide_empty' => false,
		'fields'     => 'ids',
	)
);

if ( ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term_id ) {
		wp_delete_term( $term_id, 'prtf_project_type' );
	}
}
