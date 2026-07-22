<?php
/**
 * Register Job custom post type.
 *
 * @package Jobl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the job custom post type.
 *
 * @return void
 */
function jobl_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'job-listings' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'job-listings' ),
		'menu_name'             => __( 'Jobs', 'job-listings' ),
		'name_admin_bar'        => __( 'Job', 'job-listings' ),
		'archives'              => __( 'Job Archives', 'job-listings' ),
		'attributes'            => __( 'Job Attributes', 'job-listings' ),
		'parent_item_colon'     => __( 'Parent Job:', 'job-listings' ),
		'all_items'             => __( 'All Jobs', 'job-listings' ),
		'add_new_item'          => __( 'Add New Job', 'job-listings' ),
		'add_new'               => __( 'Add New', 'job-listings' ),
		'new_item'              => __( 'New Job', 'job-listings' ),
		'edit_item'             => __( 'Edit Job', 'job-listings' ),
		'update_item'           => __( 'Update Job', 'job-listings' ),
		'view_item'             => __( 'View Job', 'job-listings' ),
		'view_items'            => __( 'View Jobs', 'job-listings' ),
		'search_items'          => __( 'Search Jobs', 'job-listings' ),
		'not_found'             => __( 'Not found', 'job-listings' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'job-listings' ),
		'featured_image'        => __( 'Featured Image', 'job-listings' ),
		'set_featured_image'    => __( 'Set featured image', 'job-listings' ),
		'remove_featured_image' => __( 'Remove featured image', 'job-listings' ),
		'use_featured_image'    => __( 'Use as featured image', 'job-listings' ),
		'insert_into_item'      => __( 'Insert into job', 'job-listings' ),
		'uploaded_to_this_item' => __( 'Uploaded to this job', 'job-listings' ),
		'items_list'            => __( 'Jobs list', 'job-listings' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'job-listings' ),
		'filter_items_list'     => __( 'Filter jobs list', 'job-listings' ),
	);

	$args = array(
		'label'               => __( 'Job', 'job-listings' ),
		'description'         => __( 'Job listings with location, employment type, and salary range', 'job-listings' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
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

	register_post_type( 'job', $args );
}
add_action( 'init', 'jobl_register_post_types' );
