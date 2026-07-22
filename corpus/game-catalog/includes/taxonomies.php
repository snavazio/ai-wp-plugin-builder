<?php
/**
 * Register custom taxonomies for Games.
 *
 * @package Gcat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the platform taxonomy.
 *
 * @return void
 */
function gcat_register_platform_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Platforms', 'Taxonomy General Name', 'game-catalog' ),
		'singular_name'              => _x( 'Platform', 'Taxonomy Singular Name', 'game-catalog' ),
		'menu_name'                  => __( 'Platforms', 'game-catalog' ),
		'all_items'                  => __( 'All Platforms', 'game-catalog' ),
		'parent_item'                => __( 'Parent Platform', 'game-catalog' ),
		'parent_item_colon'          => __( 'Parent Platform:', 'game-catalog' ),
		'new_item_name'              => __( 'New Platform Name', 'game-catalog' ),
		'add_new_item'               => __( 'Add New Platform', 'game-catalog' ),
		'edit_item'                  => __( 'Edit Platform', 'game-catalog' ),
		'update_item'                => __( 'Update Platform', 'game-catalog' ),
		'view_item'                  => __( 'View Platform', 'game-catalog' ),
		'separate_items_with_commas' => __( 'Separate platforms with commas', 'game-catalog' ),
		'add_or_remove_items'        => __( 'Add or remove platforms', 'game-catalog' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'game-catalog' ),
		'popular_items'              => __( 'Popular Platforms', 'game-catalog' ),
		'search_items'               => __( 'Search Platforms', 'game-catalog' ),
		'not_found'                  => __( 'Not Found', 'game-catalog' ),
		'no_terms'                   => __( 'No platforms', 'game-catalog' ),
		'items_list'                 => __( 'Platforms list', 'game-catalog' ),
		'items_list_navigation'      => __( 'Platforms list navigation', 'game-catalog' ),
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
		'rewrite'           => array( 'slug' => 'platform' ),
	);

	register_taxonomy( 'gcat_platform', array( 'game' ), $args );
}
add_action( 'init', 'gcat_register_platform_taxonomy' );

/**
 * Register the genre taxonomy.
 *
 * @return void
 */
function gcat_register_genre_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Genres', 'Taxonomy General Name', 'game-catalog' ),
		'singular_name'              => _x( 'Genre', 'Taxonomy Singular Name', 'game-catalog' ),
		'menu_name'                  => __( 'Genres', 'game-catalog' ),
		'all_items'                  => __( 'All Genres', 'game-catalog' ),
		'parent_item'                => __( 'Parent Genre', 'game-catalog' ),
		'parent_item_colon'          => __( 'Parent Genre:', 'game-catalog' ),
		'new_item_name'              => __( 'New Genre Name', 'game-catalog' ),
		'add_new_item'               => __( 'Add New Genre', 'game-catalog' ),
		'edit_item'                  => __( 'Edit Genre', 'game-catalog' ),
		'update_item'                => __( 'Update Genre', 'game-catalog' ),
		'view_item'                  => __( 'View Genre', 'game-catalog' ),
		'separate_items_with_commas' => __( 'Separate genres with commas', 'game-catalog' ),
		'add_or_remove_items'        => __( 'Add or remove genres', 'game-catalog' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'game-catalog' ),
		'popular_items'              => __( 'Popular Genres', 'game-catalog' ),
		'search_items'               => __( 'Search Genres', 'game-catalog' ),
		'not_found'                  => __( 'Not Found', 'game-catalog' ),
		'no_terms'                   => __( 'No genres', 'game-catalog' ),
		'items_list'                 => __( 'Genres list', 'game-catalog' ),
		'items_list_navigation'      => __( 'Genres list navigation', 'game-catalog' ),
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

	register_taxonomy( 'gcat_genre', array( 'game' ), $args );
}
add_action( 'init', 'gcat_register_genre_taxonomy' );
