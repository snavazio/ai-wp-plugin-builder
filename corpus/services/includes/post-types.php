<?php
/**
 * Register Services custom post type.
 *
 * @package Srvs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the service custom post type.
 *
 * @return void
 */
function srvs_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Services', 'Post Type General Name', 'services' ),
		'singular_name'         => _x( 'Service', 'Post Type Singular Name', 'services' ),
		'menu_name'             => __( 'Services', 'services' ),
		'name_admin_bar'        => __( 'Service', 'services' ),
		'archives'              => __( 'Service Archives', 'services' ),
		'attributes'            => __( 'Service Attributes', 'services' ),
		'parent_item_colon'     => __( 'Parent Service:', 'services' ),
		'all_items'             => __( 'All Services', 'services' ),
		'add_new_item'          => __( 'Add New Service', 'services' ),
		'add_new'               => __( 'Add New', 'services' ),
		'new_item'              => __( 'New Service', 'services' ),
		'edit_item'             => __( 'Edit Service', 'services' ),
		'update_item'           => __( 'Update Service', 'services' ),
		'view_item'             => __( 'View Service', 'services' ),
		'view_items'            => __( 'View Services', 'services' ),
		'search_items'          => __( 'Search Services', 'services' ),
		'not_found'             => __( 'Not found', 'services' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'services' ),
		'featured_image'        => __( 'Featured Image', 'services' ),
		'set_featured_image'    => __( 'Set featured image', 'services' ),
		'remove_featured_image' => __( 'Remove featured image', 'services' ),
		'use_featured_image'    => __( 'Use as featured image', 'services' ),
		'insert_into_item'      => __( 'Insert into service', 'services' ),
		'uploaded_to_this_item' => __( 'Uploaded to this service', 'services' ),
		'items_list'            => __( 'Services list', 'services' ),
		'items_list_navigation' => __( 'Services list navigation', 'services' ),
		'filter_items_list'     => __( 'Filter services list', 'services' ),
	);

	$args = array(
		'label'               => __( 'Service', 'services' ),
		'description'         => __( 'Services with price and icon', 'services' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-admin-network',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'service', $args );
}
add_action( 'init', 'srvs_register_post_types' );
