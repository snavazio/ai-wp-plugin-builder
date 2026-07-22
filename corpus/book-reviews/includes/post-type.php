<?php
/**
 * Register book review custom post type.
 *
 * @package Brpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the book_review post type.
 *
 * @return void
 */
function brpl_register_post_type() {
	$labels = array(
		'name'                  => _x( 'Book Reviews', 'Post type general name', 'book-reviews' ),
		'singular_name'         => _x( 'Book Review', 'Post type singular name', 'book-reviews' ),
		'menu_name'             => _x( 'Book Reviews', 'Admin Menu text', 'book-reviews' ),
		'name_admin_bar'        => _x( 'Book Review', 'Add New on Toolbar', 'book-reviews' ),
		'add_new'               => __( 'Add New', 'book-reviews' ),
		'add_new_item'          => __( 'Add New Book Review', 'book-reviews' ),
		'new_item'              => __( 'New Book Review', 'book-reviews' ),
		'edit_item'             => __( 'Edit Book Review', 'book-reviews' ),
		'view_item'             => __( 'View Book Review', 'book-reviews' ),
		'all_items'             => __( 'All Book Reviews', 'book-reviews' ),
		'search_items'          => __( 'Search Book Reviews', 'book-reviews' ),
		'parent_item_colon'     => __( 'Parent Book Reviews:', 'book-reviews' ),
		'not_found'             => __( 'No book reviews found.', 'book-reviews' ),
		'not_found_in_trash'    => __( 'No book reviews found in Trash.', 'book-reviews' ),
		'archives'              => _x( 'Book Review archives', 'The post type archive label used in nav menus.', 'book-reviews' ),
		'insert_into_item'      => _x( 'Insert into book review', 'Overrides the "Insert into post"/"Insert into page" phrase.', 'book-reviews' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this book review', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase.', 'book-reviews' ),
		'filter_items_list'     => _x( 'Filter book reviews list', 'Screen reader text for the filter links heading on the post type listing screen.', 'book-reviews' ),
		'items_list_navigation' => _x( 'Book reviews list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'book-reviews' ),
		'items_list'            => _x( 'Book reviews list', 'Screen reader text for the items list heading on the post type listing screen.', 'book-reviews' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-book',
		'supports'           => array( 'title', 'editor' ),
		'show_in_rest'       => false,
	);

	register_post_type( 'book_review', $args );
}
add_action( 'init', 'brpl_register_post_type' );
