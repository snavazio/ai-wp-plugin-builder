<?php
/**
 * Register custom post type for Businesses.
 *
 * @package Bdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the bdir_business custom post type.
 *
 * @return void
 */
function bdir_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Businesses', 'Post Type General Name', 'business-directory' ),
		'singular_name'         => _x( 'Business', 'Post Type Singular Name', 'business-directory' ),
		'menu_name'             => __( 'Businesses', 'business-directory' ),
		'name_admin_bar'        => __( 'Business', 'business-directory' ),
		'archives'              => __( 'Business Archives', 'business-directory' ),
		'attributes'            => __( 'Business Attributes', 'business-directory' ),
		'parent_item_colon'     => __( 'Parent Business:', 'business-directory' ),
		'all_items'             => __( 'All Businesses', 'business-directory' ),
		'add_new_item'          => __( 'Add New Business', 'business-directory' ),
		'add_new'               => __( 'Add New', 'business-directory' ),
		'new_item'              => __( 'New Business', 'business-directory' ),
		'edit_item'             => __( 'Edit Business', 'business-directory' ),
		'update_item'           => __( 'Update Business', 'business-directory' ),
		'view_item'             => __( 'View Business', 'business-directory' ),
		'view_items'            => __( 'View Businesses', 'business-directory' ),
		'search_items'          => __( 'Search Businesses', 'business-directory' ),
		'not_found'             => __( 'Not found', 'business-directory' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'business-directory' ),
		'featured_image'        => __( 'Featured Image', 'business-directory' ),
		'set_featured_image'    => __( 'Set featured image', 'business-directory' ),
		'remove_featured_image' => __( 'Remove featured image', 'business-directory' ),
		'use_featured_image'    => __( 'Use as featured image', 'business-directory' ),
		'insert_into_item'      => __( 'Insert into business', 'business-directory' ),
		'uploaded_to_this_item' => __( 'Uploaded to this business', 'business-directory' ),
		'items_list'            => __( 'Businesses list', 'business-directory' ),
		'items_list_navigation' => __( 'Businesses list navigation', 'business-directory' ),
		'filter_items_list'     => __( 'Filter businesses list', 'business-directory' ),
	);

	$args = array(
		'label'               => __( 'Business', 'business-directory' ),
		'description'         => __( 'Businesses directory with hierarchical categories and regions', 'business-directory' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-admin-site',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'business', $args );
}
add_action( 'init', 'bdir_register_post_types' );
