<?php
/**
 * Register custom taxonomies for Books.
 *
 * @package Blib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the blib_author taxonomy.
 *
 * @return void
 */
function blib_register_author_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Authors', 'Taxonomy General Name', 'book-library' ),
		'singular_name'              => _x( 'Author', 'Taxonomy Singular Name', 'book-library' ),
		'menu_name'                  => __( 'Authors', 'book-library' ),
		'all_items'                  => __( 'All Authors', 'book-library' ),
		'parent_item'                => __( 'Parent Author', 'book-library' ),
		'parent_item_colon'          => __( 'Parent Author:', 'book-library' ),
		'new_item_name'              => __( 'New Author Name', 'book-library' ),
		'add_new_item'               => __( 'Add New Author', 'book-library' ),
		'edit_item'                  => __( 'Edit Author', 'book-library' ),
		'update_item'                => __( 'Update Author', 'book-library' ),
		'view_item'                  => __( 'View Author', 'book-library' ),
		'separate_items_with_commas' => __( 'Separate authors with commas', 'book-library' ),
		'add_or_remove_items'        => __( 'Add or remove authors', 'book-library' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'book-library' ),
		'popular_items'              => __( 'Popular Authors', 'book-library' ),
		'search_items'               => __( 'Search Authors', 'book-library' ),
		'not_found'                  => __( 'Not Found', 'book-library' ),
		'no_terms'                   => __( 'No authors', 'book-library' ),
		'items_list'                 => __( 'Authors list', 'book-library' ),
		'items_list_navigation'      => __( 'Authors list navigation', 'book-library' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'author' ),
	);

	register_taxonomy( 'blib_author', array( 'blib_book' ), $args );
}

/**
 * Register the blib_genre taxonomy.
 *
 * @return void
 */
function blib_register_genre_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Genres', 'Taxonomy General Name', 'book-library' ),
		'singular_name'              => _x( 'Genre', 'Taxonomy Singular Name', 'book-library' ),
		'menu_name'                  => __( 'Genres', 'book-library' ),
		'all_items'                  => __( 'All Genres', 'book-library' ),
		'parent_item'                => __( 'Parent Genre', 'book-library' ),
		'parent_item_colon'          => __( 'Parent Genre:', 'book-library' ),
		'new_item_name'              => __( 'New Genre Name', 'book-library' ),
		'add_new_item'               => __( 'Add New Genre', 'book-library' ),
		'edit_item'                  => __( 'Edit Genre', 'book-library' ),
		'update_item'                => __( 'Update Genre', 'book-library' ),
		'view_item'                  => __( 'View Genre', 'book-library' ),
		'separate_items_with_commas' => __( 'Separate genres with commas', 'book-library' ),
		'add_or_remove_items'        => __( 'Add or remove genres', 'book-library' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'book-library' ),
		'popular_items'              => __( 'Popular Genres', 'book-library' ),
		'search_items'               => __( 'Search Genres', 'book-library' ),
		'not_found'                  => __( 'Not Found', 'book-library' ),
		'no_terms'                   => __( 'No genres', 'book-library' ),
		'items_list'                 => __( 'Genres list', 'book-library' ),
		'items_list_navigation'      => __( 'Genres list navigation', 'book-library' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'genre' ),
	);

	register_taxonomy( 'blib_genre', array( 'blib_book' ), $args );
}

add_action( 'init', 'blib_register_author_taxonomy' );
add_action( 'init', 'blib_register_genre_taxonomy' );
