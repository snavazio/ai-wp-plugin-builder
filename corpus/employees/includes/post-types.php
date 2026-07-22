<?php
/**
 * Register Employee custom post type.
 *
 * @package Empc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the employee custom post type.
 *
 * @return void
 */
function empc_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Employees', 'Post Type General Name', 'employees' ),
		'singular_name'         => _x( 'Employee', 'Post Type Singular Name', 'employees' ),
		'menu_name'             => __( 'Employees', 'employees' ),
		'name_admin_bar'        => __( 'Employee', 'employees' ),
		'archives'              => __( 'Employee Archives', 'employees' ),
		'attributes'            => __( 'Employee Attributes', 'employees' ),
		'parent_item_colon'     => __( 'Parent Employee:', 'employees' ),
		'all_items'             => __( 'All Employees', 'employees' ),
		'add_new_item'          => __( 'Add New Employee', 'employees' ),
		'add_new'               => __( 'Add New', 'employees' ),
		'new_item'              => __( 'New Employee', 'employees' ),
		'edit_item'             => __( 'Edit Employee', 'employees' ),
		'update_item'           => __( 'Update Employee', 'employees' ),
		'view_item'             => __( 'View Employee', 'employees' ),
		'view_items'            => __( 'View Employees', 'employees' ),
		'search_items'          => __( 'Search Employees', 'employees' ),
		'not_found'             => __( 'Not found', 'employees' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'employees' ),
		'featured_image'        => __( 'Featured Image', 'employees' ),
		'set_featured_image'    => __( 'Set featured image', 'employees' ),
		'remove_featured_image' => __( 'Remove featured image', 'employees' ),
		'use_featured_image'    => __( 'Use as featured image', 'employees' ),
		'insert_into_item'      => __( 'Insert into employee', 'employees' ),
		'uploaded_to_this_item' => __( 'Uploaded to this employee', 'employees' ),
		'items_list'            => __( 'Employees list', 'employees' ),
		'items_list_navigation' => __( 'Employees list navigation', 'employees' ),
		'filter_items_list'     => __( 'Filter employees list', 'employees' ),
	);

	$args = array(
		'label'               => __( 'Employee', 'employees' ),
		'description'         => __( 'Employee records with department and email', 'employees' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-business',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'employee', $args );
}
add_action( 'init', 'empc_register_post_types' );
