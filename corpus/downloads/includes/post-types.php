<?php
/**
 * Register Download custom post type.
 *
 * @package Dlm1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the download custom post type.
 *
 * @return void
 */
function dlm1_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Downloads', 'Post Type General Name', 'downloads' ),
		'singular_name'         => _x( 'Download', 'Post Type Singular Name', 'downloads' ),
		'menu_name'             => __( 'Downloads', 'downloads' ),
		'name_admin_bar'        => __( 'Download', 'downloads' ),
		'archives'              => __( 'Download Archives', 'downloads' ),
		'attributes'            => __( 'Download Attributes', 'downloads' ),
		'parent_item_colon'     => __( 'Parent Download:', 'downloads' ),
		'all_items'             => __( 'All Downloads', 'downloads' ),
		'add_new_item'          => __( 'Add New Download', 'downloads' ),
		'add_new'               => __( 'Add New', 'downloads' ),
		'new_item'              => __( 'New Download', 'downloads' ),
		'edit_item'             => __( 'Edit Download', 'downloads' ),
		'update_item'           => __( 'Update Download', 'downloads' ),
		'view_item'             => __( 'View Download', 'downloads' ),
		'view_items'            => __( 'View Downloads', 'downloads' ),
		'search_items'          => __( 'Search Downloads', 'downloads' ),
		'not_found'             => __( 'Not found', 'downloads' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'downloads' ),
		'featured_image'        => __( 'Featured Image', 'downloads' ),
		'set_featured_image'    => __( 'Set featured image', 'downloads' ),
		'remove_featured_image' => __( 'Remove featured image', 'downloads' ),
		'use_featured_image'    => __( 'Use as featured image', 'downloads' ),
		'insert_into_item'      => __( 'Insert into download', 'downloads' ),
		'uploaded_to_this_item' => __( 'Uploaded to this download', 'downloads' ),
		'items_list'            => __( 'Downloads list', 'downloads' ),
		'items_list_navigation' => __( 'Downloads list navigation', 'downloads' ),
		'filter_items_list'     => __( 'Filter downloads list', 'downloads' ),
	);

	$args = array(
		'label'               => __( 'Download', 'downloads' ),
		'description'         => __( 'Downloads with file URL and download count', 'downloads' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-download',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'download', $args );
}
add_action( 'init', 'dlm1_register_post_types' );
