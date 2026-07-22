<?php
/**
 * Register Vehicle custom post type.
 *
 * @package Vclt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the vehicle custom post type.
 *
 * @return void
 */
function vclt_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Vehicles', 'Post Type General Name', 'vehicles' ),
		'singular_name'         => _x( 'Vehicle', 'Post Type Singular Name', 'vehicles' ),
		'menu_name'             => __( 'Vehicles', 'vehicles' ),
		'name_admin_bar'        => __( 'Vehicle', 'vehicles' ),
		'archives'              => __( 'Vehicle Archives', 'vehicles' ),
		'attributes'            => __( 'Vehicle Attributes', 'vehicles' ),
		'parent_item_colon'     => __( 'Parent Vehicle:', 'vehicles' ),
		'all_items'             => __( 'All Vehicles', 'vehicles' ),
		'add_new_item'          => __( 'Add New Vehicle', 'vehicles' ),
		'add_new'               => __( 'Add New', 'vehicles' ),
		'new_item'              => __( 'New Vehicle', 'vehicles' ),
		'edit_item'             => __( 'Edit Vehicle', 'vehicles' ),
		'update_item'           => __( 'Update Vehicle', 'vehicles' ),
		'view_item'             => __( 'View Vehicle', 'vehicles' ),
		'view_items'            => __( 'View Vehicles', 'vehicles' ),
		'search_items'          => __( 'Search Vehicles', 'vehicles' ),
		'not_found'             => __( 'Not found', 'vehicles' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'vehicles' ),
		'featured_image'        => __( 'Featured Image', 'vehicles' ),
		'set_featured_image'    => __( 'Set featured image', 'vehicles' ),
		'remove_featured_image' => __( 'Remove featured image', 'vehicles' ),
		'use_featured_image'    => __( 'Use as featured image', 'vehicles' ),
		'insert_into_item'      => __( 'Insert into vehicle', 'vehicles' ),
		'uploaded_to_this_item' => __( 'Uploaded to this vehicle', 'vehicles' ),
		'items_list'            => __( 'Vehicles list', 'vehicles' ),
		'items_list_navigation' => __( 'Vehicles list navigation', 'vehicles' ),
		'filter_items_list'     => __( 'Filter vehicles list', 'vehicles' ),
	);

	$args = array(
		'label'               => __( 'Vehicle', 'vehicles' ),
		'description'         => __( 'Vehicles with make, model, and year', 'vehicles' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-vehicle',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'vehicle', $args );
}
add_action( 'init', 'vclt_register_post_types' );
