<?php
/**
 * Register custom post type for Articles.
 *
 * @package Kbpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the kbpl_article custom post type.
 *
 * @return void
 */
function kbpl_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Articles', 'Post Type General Name', 'knowledge-base' ),
		'singular_name'         => _x( 'Article', 'Post Type Singular Name', 'knowledge-base' ),
		'menu_name'             => __( 'Articles', 'knowledge-base' ),
		'name_admin_bar'        => __( 'Article', 'knowledge-base' ),
		'archives'              => __( 'Article Archives', 'knowledge-base' ),
		'attributes'            => __( 'Article Attributes', 'knowledge-base' ),
		'parent_item_colon'     => __( 'Parent Article:', 'knowledge-base' ),
		'all_items'             => __( 'All Articles', 'knowledge-base' ),
		'add_new_item'          => __( 'Add New Article', 'knowledge-base' ),
		'add_new'               => __( 'Add New', 'knowledge-base' ),
		'new_item'              => __( 'New Article', 'knowledge-base' ),
		'edit_item'             => __( 'Edit Article', 'knowledge-base' ),
		'update_item'           => __( 'Update Article', 'knowledge-base' ),
		'view_item'             => __( 'View Article', 'knowledge-base' ),
		'view_items'            => __( 'View Articles', 'knowledge-base' ),
		'search_items'          => __( 'Search Articles', 'knowledge-base' ),
		'not_found'             => __( 'Not found', 'knowledge-base' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowledge-base' ),
		'featured_image'        => __( 'Featured Image', 'knowledge-base' ),
		'set_featured_image'    => __( 'Set featured image', 'knowledge-base' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowledge-base' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowledge-base' ),
		'insert_into_item'      => __( 'Insert into article', 'knowledge-base' ),
		'uploaded_to_this_item' => __( 'Uploaded to this article', 'knowledge-base' ),
		'items_list'            => __( 'Articles list', 'knowledge-base' ),
		'items_list_navigation' => __( 'Articles list navigation', 'knowledge-base' ),
		'filter_items_list'     => __( 'Filter articles list', 'knowledge-base' ),
	);

	$args = array(
		'label'               => __( 'Article', 'knowledge-base' ),
		'description'         => __( 'Knowledge base articles organized by topic', 'knowledge-base' ),
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

	register_post_type( 'kbpl_article', $args );
}
add_action( 'init', 'kbpl_register_post_types' );
