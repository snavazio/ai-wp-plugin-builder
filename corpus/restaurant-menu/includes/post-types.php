<?php
/**
 * Register Restaurant Menu custom post type.
 *
 * @package Rmenu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the menu_item custom post type.
 *
 * @return void
 */
function rmenu_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Menu Items', 'Post Type General Name', 'restaurant-menu' ),
		'singular_name'         => _x( 'Menu Item', 'Post Type Singular Name', 'restaurant-menu' ),
		'menu_name'             => __( 'Menu Items', 'restaurant-menu' ),
		'name_admin_bar'        => __( 'Menu Item', 'restaurant-menu' ),
		'archives'              => __( 'Menu Item Archives', 'restaurant-menu' ),
		'attributes'            => __( 'Menu Item Attributes', 'restaurant-menu' ),
		'parent_item_colon'     => __( 'Parent Menu Item:', 'restaurant-menu' ),
		'all_items'             => __( 'All Menu Items', 'restaurant-menu' ),
		'add_new_item'          => __( 'Add New Menu Item', 'restaurant-menu' ),
		'add_new'               => __( 'Add New', 'restaurant-menu' ),
		'new_item'              => __( 'New Menu Item', 'restaurant-menu' ),
		'edit_item'             => __( 'Edit Menu Item', 'restaurant-menu' ),
		'update_item'           => __( 'Update Menu Item', 'restaurant-menu' ),
		'view_item'             => __( 'View Menu Item', 'restaurant-menu' ),
		'view_items'            => __( 'View Menu Items', 'restaurant-menu' ),
		'search_items'          => __( 'Search Menu Items', 'restaurant-menu' ),
		'not_found'             => __( 'Not found', 'restaurant-menu' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'restaurant-menu' ),
		'featured_image'        => __( 'Featured Image', 'restaurant-menu' ),
		'set_featured_image'    => __( 'Set featured image', 'restaurant-menu' ),
		'remove_featured_image' => __( 'Remove featured image', 'restaurant-menu' ),
		'use_featured_image'    => __( 'Use as featured image', 'restaurant-menu' ),
		'insert_into_item'      => __( 'Insert into menu item', 'restaurant-menu' ),
		'uploaded_to_this_item' => __( 'Uploaded to this menu item', 'restaurant-menu' ),
		'items_list'            => __( 'Menu Items list', 'restaurant-menu' ),
		'items_list_navigation' => __( 'Menu Items list navigation', 'restaurant-menu' ),
		'filter_items_list'     => __( 'Filter menu items list', 'restaurant-menu' ),
	);

	$args = array(
		'label'               => __( 'Menu Item', 'restaurant-menu' ),
		'description'         => __( 'Restaurant menu items with price and dietary notes', 'restaurant-menu' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-restaurant',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'menu_item', $args );
}
add_action( 'init', 'rmenu_register_post_types' );
