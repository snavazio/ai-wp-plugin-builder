<?php
/**
 * Register custom post type for Comics.
 *
 * @package Comics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the comic custom post type.
 *
 * @return void
 */
function comics_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Comics', 'Post Type General Name', 'comics' ),
		'singular_name'         => _x( 'Comic', 'Post Type Singular Name', 'comics' ),
		'menu_name'             => __( 'Comics', 'comics' ),
		'name_admin_bar'        => __( 'Comic', 'comics' ),
		'archives'              => __( 'Comic Archives', 'comics' ),
		'attributes'            => __( 'Comic Attributes', 'comics' ),
		'parent_item_colon'     => __( 'Parent Comic:', 'comics' ),
		'all_items'             => __( 'All Comics', 'comics' ),
		'add_new_item'          => __( 'Add New Comic', 'comics' ),
		'add_new'               => __( 'Add New', 'comics' ),
		'new_item'              => __( 'New Comic', 'comics' ),
		'edit_item'             => __( 'Edit Comic', 'comics' ),
		'update_item'           => __( 'Update Comic', 'comics' ),
		'view_item'             => __( 'View Comic', 'comics' ),
		'view_items'            => __( 'View Comics', 'comics' ),
		'search_items'          => __( 'Search Comics', 'comics' ),
		'not_found'             => __( 'Not found', 'comics' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'comics' ),
		'featured_image'        => __( 'Featured Image', 'comics' ),
		'set_featured_image'    => __( 'Set featured image', 'comics' ),
		'remove_featured_image' => __( 'Remove featured image', 'comics' ),
		'use_featured_image'    => __( 'Use as featured image', 'comics' ),
		'insert_into_item'      => __( 'Insert into comic', 'comics' ),
		'uploaded_to_this_item' => __( 'Uploaded to this comic', 'comics' ),
		'items_list'            => __( 'Comics list', 'comics' ),
		'items_list_navigation' => __( 'Comics list navigation', 'comics' ),
		'filter_items_list'     => __( 'Filter comics list', 'comics' ),
	);

	$args = array(
		'label'               => __( 'Comic', 'comics' ),
		'description'         => __( 'Comics with issue number meta, publisher, and series', 'comics' ),
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

	register_post_type( 'comic', $args );
}
add_action( 'init', 'comics_register_post_types' );
