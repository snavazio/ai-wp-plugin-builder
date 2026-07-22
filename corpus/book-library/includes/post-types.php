<?php
/**
 * Register custom post type for Books.
 *
 * @package Blib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the blib_book custom post type.
 *
 * @return void
 */
function blib_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Books', 'Post Type General Name', 'book-library' ),
		'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'book-library' ),
		'menu_name'             => __( 'Books', 'book-library' ),
		'name_admin_bar'        => __( 'Book', 'book-library' ),
		'archives'              => __( 'Book Archives', 'book-library' ),
		'attributes'            => __( 'Book Attributes', 'book-library' ),
		'parent_item_colon'     => __( 'Parent Book:', 'book-library' ),
		'all_items'             => __( 'All Books', 'book-library' ),
		'add_new_item'          => __( 'Add New Book', 'book-library' ),
		'add_new'               => __( 'Add New', 'book-library' ),
		'new_item'              => __( 'New Book', 'book-library' ),
		'edit_item'             => __( 'Edit Book', 'book-library' ),
		'update_item'           => __( 'Update Book', 'book-library' ),
		'view_item'             => __( 'View Book', 'book-library' ),
		'view_items'            => __( 'View Books', 'book-library' ),
		'search_items'          => __( 'Search Books', 'book-library' ),
		'not_found'             => __( 'Not found', 'book-library' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'book-library' ),
		'featured_image'        => __( 'Featured Image', 'book-library' ),
		'set_featured_image'    => __( 'Set featured image', 'book-library' ),
		'remove_featured_image' => __( 'Remove featured image', 'book-library' ),
		'use_featured_image'    => __( 'Use as featured image', 'book-library' ),
		'insert_into_item'      => __( 'Insert into book', 'book-library' ),
		'uploaded_to_this_item' => __( 'Uploaded to this book', 'book-library' ),
		'items_list'            => __( 'Books list', 'book-library' ),
		'items_list_navigation' => __( 'Books list navigation', 'book-library' ),
		'filter_items_list'     => __( 'Filter books list', 'book-library' ),
	);

	$args = array(
		'label'               => __( 'Book', 'book-library' ),
		'description'         => __( 'Books managed by the Book Library plugin', 'book-library' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-book',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'blib_book', $args );
}
add_action( 'init', 'blib_register_post_types' );
