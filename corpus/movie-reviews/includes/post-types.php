<?php
/**
 * Register custom post type for Movies.
 *
 * @package Mrev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the movie custom post type.
 *
 * @return void
 */
function mrev_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Movies', 'Post Type General Name', 'movie-reviews' ),
		'singular_name'         => _x( 'Movie', 'Post Type Singular Name', 'movie-reviews' ),
		'menu_name'             => __( 'Movies', 'movie-reviews' ),
		'name_admin_bar'        => __( 'Movie', 'movie-reviews' ),
		'archives'              => __( 'Movie Archives', 'movie-reviews' ),
		'attributes'            => __( 'Movie Attributes', 'movie-reviews' ),
		'parent_item_colon'     => __( 'Parent Movie:', 'movie-reviews' ),
		'all_items'             => __( 'All Movies', 'movie-reviews' ),
		'add_new_item'          => __( 'Add New Movie', 'movie-reviews' ),
		'add_new'               => __( 'Add New', 'movie-reviews' ),
		'new_item'              => __( 'New Movie', 'movie-reviews' ),
		'edit_item'             => __( 'Edit Movie', 'movie-reviews' ),
		'update_item'           => __( 'Update Movie', 'movie-reviews' ),
		'view_item'             => __( 'View Movie', 'movie-reviews' ),
		'view_items'            => __( 'View Movies', 'movie-reviews' ),
		'search_items'          => __( 'Search Movies', 'movie-reviews' ),
		'not_found'             => __( 'Not found', 'movie-reviews' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'movie-reviews' ),
		'featured_image'        => __( 'Featured Image', 'movie-reviews' ),
		'set_featured_image'    => __( 'Set featured image', 'movie-reviews' ),
		'remove_featured_image' => __( 'Remove featured image', 'movie-reviews' ),
		'use_featured_image'    => __( 'Use as featured image', 'movie-reviews' ),
		'insert_into_item'      => __( 'Insert into movie', 'movie-reviews' ),
		'uploaded_to_this_item' => __( 'Uploaded to this movie', 'movie-reviews' ),
		'items_list'            => __( 'Movies list', 'movie-reviews' ),
		'items_list_navigation' => __( 'Movies list navigation', 'movie-reviews' ),
		'filter_items_list'     => __( 'Filter movies list', 'movie-reviews' ),
	);

	$args = array(
		'label'               => __( 'Movie', 'movie-reviews' ),
		'description'         => __( 'Movies with rating meta and genre taxonomy', 'movie-reviews' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-video-alt3',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'movie', $args );
}
add_action( 'init', 'mrev_register_post_types' );
