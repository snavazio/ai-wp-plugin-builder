<?php
/**
 * Register custom taxonomy for Movie Genres.
 *
 * @package Mrev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the mrev_genre taxonomy.
 *
 * @return void
 */
function mrev_register_genre_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Genres', 'Taxonomy General Name', 'movie-reviews' ),
		'singular_name'              => _x( 'Genre', 'Taxonomy Singular Name', 'movie-reviews' ),
		'menu_name'                  => __( 'Genres', 'movie-reviews' ),
		'all_items'                  => __( 'All Genres', 'movie-reviews' ),
		'parent_item'                => __( 'Parent Genre', 'movie-reviews' ),
		'parent_item_colon'          => __( 'Parent Genre:', 'movie-reviews' ),
		'new_item_name'              => __( 'New Genre Name', 'movie-reviews' ),
		'add_new_item'               => __( 'Add New Genre', 'movie-reviews' ),
		'edit_item'                  => __( 'Edit Genre', 'movie-reviews' ),
		'update_item'                => __( 'Update Genre', 'movie-reviews' ),
		'view_item'                  => __( 'View Genre', 'movie-reviews' ),
		'separate_items_with_commas' => __( 'Separate genres with commas', 'movie-reviews' ),
		'add_or_remove_items'        => __( 'Add or remove genres', 'movie-reviews' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'movie-reviews' ),
		'popular_items'              => __( 'Popular Genres', 'movie-reviews' ),
		'search_items'               => __( 'Search Genres', 'movie-reviews' ),
		'not_found'                  => __( 'Not Found', 'movie-reviews' ),
		'no_terms'                   => __( 'No genres', 'movie-reviews' ),
		'items_list'                 => __( 'Genres list', 'movie-reviews' ),
		'items_list_navigation'      => __( 'Genres list navigation', 'movie-reviews' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'genre' ),
	);

	register_taxonomy( 'mrev_genre', array( 'movie' ), $args );
}
add_action( 'init', 'mrev_register_genre_taxonomy' );
