<?php
/**
 * Register Staff custom post type.
 *
 * @package Staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the staff custom post type.
 *
 * @return void
 */
function staff_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Staff', 'Post Type General Name', 'staff-directory' ),
		'singular_name'         => _x( 'Staff Member', 'Post Type Singular Name', 'staff-directory' ),
		'menu_name'             => __( 'Staff', 'staff-directory' ),
		'name_admin_bar'        => __( 'Staff Member', 'staff-directory' ),
		'archives'              => __( 'Staff Archives', 'staff-directory' ),
		'attributes'            => __( 'Staff Attributes', 'staff-directory' ),
		'parent_item_colon'     => __( 'Parent Staff Member:', 'staff-directory' ),
		'all_items'             => __( 'All Staff', 'staff-directory' ),
		'add_new_item'          => __( 'Add New Staff Member', 'staff-directory' ),
		'add_new'               => __( 'Add New', 'staff-directory' ),
		'new_item'              => __( 'New Staff Member', 'staff-directory' ),
		'edit_item'             => __( 'Edit Staff Member', 'staff-directory' ),
		'update_item'           => __( 'Update Staff Member', 'staff-directory' ),
		'view_item'             => __( 'View Staff Member', 'staff-directory' ),
		'view_items'            => __( 'View Staff', 'staff-directory' ),
		'search_items'          => __( 'Search Staff', 'staff-directory' ),
		'not_found'             => __( 'Not found', 'staff-directory' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'staff-directory' ),
		'featured_image'        => __( 'Featured Image', 'staff-directory' ),
		'set_featured_image'    => __( 'Set featured image', 'staff-directory' ),
		'remove_featured_image' => __( 'Remove featured image', 'staff-directory' ),
		'use_featured_image'    => __( 'Use as featured image', 'staff-directory' ),
		'insert_into_item'      => __( 'Insert into staff member', 'staff-directory' ),
		'uploaded_to_this_item' => __( 'Uploaded to this staff member', 'staff-directory' ),
		'items_list'            => __( 'Staff list', 'staff-directory' ),
		'items_list_navigation' => __( 'Staff list navigation', 'staff-directory' ),
		'filter_items_list'     => __( 'Filter staff list', 'staff-directory' ),
	);

	$args = array(
		'label'               => __( 'Staff Member', 'staff-directory' ),
		'description'         => __( 'Staff members with job title and phone number', 'staff-directory' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-businessman',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'staff', $args );
}
add_action( 'init', 'staff_register_post_types' );
