<?php
/**
 * Uninstall cleanup for Knowledge Base Plugin.
 *
 * @package Kbpl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all article posts.
$articles = get_posts(
	array(
		'post_type'      => 'kbpl_article',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $articles as $article_id ) {
	wp_delete_post( $article_id, true );
}

// Delete all topic taxonomy terms.
$terms = get_terms(
	array(
		'taxonomy'   => 'kbpl_topic',
		'hide_empty' => false,
		'fields'     => 'ids',
	)
);

if ( ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term_id ) {
		wp_delete_term( $term_id, 'kbpl_topic' );
	}
}
