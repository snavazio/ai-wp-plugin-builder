<?php
/**
 * Register Rental custom post type.
 *
 * @package Rent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the rental custom post type.
 *
 * @return void
 */
function rent_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Rentals', 'Post Type General Name', 'rentals' ),
		'singular_name'         => _x( 'Rental', 'Post Type Singular Name', 'rentals' ),
		'menu_name'             => __( 'Rentals', 'rentals' ),
		'name_admin_bar'        => __( 'Rental', 'rentals' ),
		'archives'              => __( 'Rental Archives', 'rentals' ),
		'attributes'            => __( 'Rental Attributes', 'rentals' ),
		'parent_item_colon'     => __( 'Parent Rental:', 'rentals' ),
		'all_items'             => __( 'All Rentals', 'rentals' ),
		'add_new_item'          => __( 'Add New Rental', 'rentals' ),
		'add_new'               => __( 'Add New', 'rentals' ),
		'new_item'              => __( 'New Rental', 'rentals' ),
		'edit_item'             => __( 'Edit Rental', 'rentals' ),
		'update_item'           => __( 'Update Rental', 'rentals' ),
		'view_item'             => __( 'View Rental', 'rentals' ),
		'view_items'            => __( 'View Rentals', 'rentals' ),
		'search_items'          => __( 'Search Rentals', 'rentals' ),
		'not_found'             => __( 'Not found', 'rentals' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'rentals' ),
		'featured_image'        => __( 'Featured Image', 'rentals' ),
		'set_featured_image'    => __( 'Set featured image', 'rentals' ),
		'remove_featured_image' => __( 'Remove featured image', 'rentals' ),
		'use_featured_image'    => __( 'Use as featured image', 'rentals' ),
		'insert_into_item'      => __( 'Insert into rental', 'rentals' ),
		'uploaded_to_this_item' => __( 'Uploaded to this rental', 'rentals' ),
		'items_list'            => __( 'Rentals list', 'rentals' ),
		'items_list_navigation' => __( 'Rentals list navigation', 'rentals' ),
		'filter_items_list'     => __( 'Filter rentals list', 'rentals' ),
	);

	$args = array(
		'label'               => __( 'Rental', 'rentals' ),
		'description'         => __( 'Rentals with price and bedrooms', 'rentals' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-admin-home',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'rental', $args );
}
add_action( 'init', 'rent_register_post_types' );
