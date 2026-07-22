<?php
/**
 * Register Case Study custom post type.
 *
 * @package Cstuds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the case-study custom post type.
 *
 * @return void
 */
function cstuds_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Case Studies', 'Post Type General Name', 'case-studies' ),
		'singular_name'         => _x( 'Case Study', 'Post Type Singular Name', 'case-studies' ),
		'menu_name'             => __( 'Case Studies', 'case-studies' ),
		'name_admin_bar'        => __( 'Case Study', 'case-studies' ),
		'archives'              => __( 'Case Study Archives', 'case-studies' ),
		'attributes'            => __( 'Case Study Attributes', 'case-studies' ),
		'parent_item_colon'     => __( 'Parent Case Study:', 'case-studies' ),
		'all_items'             => __( 'All Case Studies', 'case-studies' ),
		'add_new_item'          => __( 'Add New Case Study', 'case-studies' ),
		'add_new'               => __( 'Add New', 'case-studies' ),
		'new_item'              => __( 'New Case Study', 'case-studies' ),
		'edit_item'             => __( 'Edit Case Study', 'case-studies' ),
		'update_item'           => __( 'Update Case Study', 'case-studies' ),
		'view_item'             => __( 'View Case Study', 'case-studies' ),
		'view_items'            => __( 'View Case Studies', 'case-studies' ),
		'search_items'          => __( 'Search Case Studies', 'case-studies' ),
		'not_found'             => __( 'Not found', 'case-studies' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'case-studies' ),
		'featured_image'        => __( 'Featured Image', 'case-studies' ),
		'set_featured_image'    => __( 'Set featured image', 'case-studies' ),
		'remove_featured_image' => __( 'Remove featured image', 'case-studies' ),
		'use_featured_image'    => __( 'Use as featured image', 'case-studies' ),
		'insert_into_item'      => __( 'Insert into case study', 'case-studies' ),
		'uploaded_to_this_item' => __( 'Uploaded to this case study', 'case-studies' ),
		'items_list'            => __( 'Case Studies list', 'case-studies' ),
		'items_list_navigation' => __( 'Case Studies list navigation', 'case-studies' ),
		'filter_items_list'     => __( 'Filter case studies list', 'case-studies' ),
	);

	$args = array(
		'label'               => __( 'Case Study', 'case-studies' ),
		'description'         => __( 'Case studies with client and outcome', 'case-studies' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-portfolio',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'case-study', $args );
}
add_action( 'init', 'cstuds_register_post_types' );
