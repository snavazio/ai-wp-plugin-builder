<?php
/**
 * Register custom post type for Tutorials.
 *
 * @package Tutly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the tutorials custom post type.
 *
 * @return void
 */
function tutly_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Tutorials', 'Post Type General Name', 'tutorials' ),
		'singular_name'         => _x( 'Tutorial', 'Post Type Singular Name', 'tutorials' ),
		'menu_name'             => __( 'Tutorials', 'tutorials' ),
		'name_admin_bar'        => __( 'Tutorial', 'tutorials' ),
		'archives'              => __( 'Tutorial Archives', 'tutorials' ),
		'attributes'            => __( 'Tutorial Attributes', 'tutorials' ),
		'parent_item_colon'     => __( 'Parent Tutorial:', 'tutorials' ),
		'all_items'             => __( 'All Tutorials', 'tutorials' ),
		'add_new_item'          => __( 'Add New Tutorial', 'tutorials' ),
		'add_new'               => __( 'Add New', 'tutorials' ),
		'new_item'              => __( 'New Tutorial', 'tutorials' ),
		'edit_item'             => __( 'Edit Tutorial', 'tutorials' ),
		'update_item'           => __( 'Update Tutorial', 'tutorials' ),
		'view_item'             => __( 'View Tutorial', 'tutorials' ),
		'view_items'            => __( 'View Tutorials', 'tutorials' ),
		'search_items'          => __( 'Search Tutorials', 'tutorials' ),
		'not_found'             => __( 'Not found', 'tutorials' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'tutorials' ),
		'featured_image'        => __( 'Featured Image', 'tutorials' ),
		'set_featured_image'    => __( 'Set featured image', 'tutorials' ),
		'remove_featured_image' => __( 'Remove featured image', 'tutorials' ),
		'use_featured_image'    => __( 'Use as featured image', 'tutorials' ),
		'insert_into_item'      => __( 'Insert into tutorial', 'tutorials' ),
		'uploaded_to_this_item' => __( 'Uploaded to this tutorial', 'tutorials' ),
		'items_list'            => __( 'Tutorials list', 'tutorials' ),
		'items_list_navigation' => __( 'Tutorials list navigation', 'tutorials' ),
		'filter_items_list'     => __( 'Filter tutorials list', 'tutorials' ),
	);

	$args = array(
		'label'               => __( 'Tutorial', 'tutorials' ),
		'description'         => __( 'Custom post type for tutorials with difficulty taxonomy', 'tutorials' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-welcome-learn-more',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'tutorials', $args );
}
add_action( 'init', 'tutly_register_post_types' );
