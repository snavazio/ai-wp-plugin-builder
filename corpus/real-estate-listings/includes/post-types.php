<?php
/**
 * Register Real Estate Property custom post type.
 *
 * @package Reli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the property custom post type.
 *
 * @return void
 */
function reli_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Properties', 'Post Type General Name', 'real-estate-listings' ),
		'singular_name'         => _x( 'Property', 'Post Type Singular Name', 'real-estate-listings' ),
		'menu_name'             => __( 'Properties', 'real-estate-listings' ),
		'name_admin_bar'        => __( 'Property', 'real-estate-listings' ),
		'archives'              => __( 'Property Archives', 'real-estate-listings' ),
		'attributes'            => __( 'Property Attributes', 'real-estate-listings' ),
		'parent_item_colon'     => __( 'Parent Property:', 'real-estate-listings' ),
		'all_items'             => __( 'All Properties', 'real-estate-listings' ),
		'add_new_item'          => __( 'Add New Property', 'real-estate-listings' ),
		'add_new'               => __( 'Add New', 'real-estate-listings' ),
		'new_item'              => __( 'New Property', 'real-estate-listings' ),
		'edit_item'             => __( 'Edit Property', 'real-estate-listings' ),
		'update_item'           => __( 'Update Property', 'real-estate-listings' ),
		'view_item'             => __( 'View Property', 'real-estate-listings' ),
		'view_items'            => __( 'View Properties', 'real-estate-listings' ),
		'search_items'          => __( 'Search Properties', 'real-estate-listings' ),
		'not_found'             => __( 'Not found', 'real-estate-listings' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'real-estate-listings' ),
		'featured_image'        => __( 'Featured Image', 'real-estate-listings' ),
		'set_featured_image'    => __( 'Set featured image', 'real-estate-listings' ),
		'remove_featured_image' => __( 'Remove featured image', 'real-estate-listings' ),
		'use_featured_image'    => __( 'Use as featured image', 'real-estate-listings' ),
		'insert_into_item'      => __( 'Insert into property', 'real-estate-listings' ),
		'uploaded_to_this_item' => __( 'Uploaded to this property', 'real-estate-listings' ),
		'items_list'            => __( 'Properties list', 'real-estate-listings' ),
		'items_list_navigation' => __( 'Properties list navigation', 'real-estate-listings' ),
		'filter_items_list'     => __( 'Filter properties list', 'real-estate-listings' ),
	);

	$args = array(
		'label'               => __( 'Property', 'real-estate-listings' ),
		'description'         => __( 'Real estate properties with price, bedrooms, and bathrooms', 'real-estate-listings' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-building',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'property', $args );
}
add_action( 'init', 'reli_register_post_types' );
