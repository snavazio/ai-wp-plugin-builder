<?php
/**
 * Register custom post type for Courses.
 *
 * @package Crs1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the crs1_course custom post type.
 *
 * @return void
 */
function crs1_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Courses', 'Post Type General Name', 'courses' ),
		'singular_name'         => _x( 'Course', 'Post Type Singular Name', 'courses' ),
		'menu_name'             => __( 'Courses', 'courses' ),
		'name_admin_bar'        => __( 'Course', 'courses' ),
		'archives'              => __( 'Course Archives', 'courses' ),
		'attributes'            => __( 'Course Attributes', 'courses' ),
		'parent_item_colon'     => __( 'Parent Course:', 'courses' ),
		'all_items'             => __( 'All Courses', 'courses' ),
		'add_new_item'          => __( 'Add New Course', 'courses' ),
		'add_new'               => __( 'Add New', 'courses' ),
		'new_item'              => __( 'New Course', 'courses' ),
		'edit_item'             => __( 'Edit Course', 'courses' ),
		'update_item'           => __( 'Update Course', 'courses' ),
		'view_item'             => __( 'View Course', 'courses' ),
		'view_items'            => __( 'View Courses', 'courses' ),
		'search_items'          => __( 'Search Courses', 'courses' ),
		'not_found'             => __( 'Not found', 'courses' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'courses' ),
		'featured_image'        => __( 'Featured Image', 'courses' ),
		'set_featured_image'    => __( 'Set featured image', 'courses' ),
		'remove_featured_image' => __( 'Remove featured image', 'courses' ),
		'use_featured_image'    => __( 'Use as featured image', 'courses' ),
		'insert_into_item'      => __( 'Insert into course', 'courses' ),
		'uploaded_to_this_item' => __( 'Uploaded to this course', 'courses' ),
		'items_list'            => __( 'Courses list', 'courses' ),
		'items_list_navigation' => __( 'Courses list navigation', 'courses' ),
		'filter_items_list'     => __( 'Filter courses list', 'courses' ),
	);

	$args = array(
		'label'               => __( 'Course', 'courses' ),
		'description'         => __( 'A course in the course system', 'courses' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
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

	register_post_type( 'crs1_course', $args );
}
add_action( 'init', 'crs1_register_post_types' );
