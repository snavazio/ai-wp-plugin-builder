<?php
/**
 * Uninstall cleanup for Wine Catalog.
 *
 * @package Winec
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all wine posts.
$wines = get_posts(
	array(
		'post_type'        => 'wine',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $wines as $wine_id ) {
	wp_delete_post( $wine_id, true );
}

// Delete all terms in region taxonomy.
$region_terms = get_terms(
	array(
		'taxonomy'   => 'region',
		'hide_empty' => false,
	)
);

foreach ( $region_terms as $region_term ) {
	wp_delete_term( $region_term->term_id, 'region' );
}

// Delete all terms in varietal taxonomy.
$varietal_terms = get_terms(
	array(
		'taxonomy'   => 'varietal',
		'hide_empty' => false,
	)
);

foreach ( $varietal_terms as $varietal_term ) {
	wp_delete_term( $varietal_term->term_id, 'varietal' );
}
