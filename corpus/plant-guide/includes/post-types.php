<?php
/**
 * Register custom post type for Plants.
 *
 * @package Pgui
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the pgui_plant custom post type.
 *
 * @return void
 */
function pgui_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Plants', 'Post Type General Name', 'plant-guide' ),
		'singular_name'         => _x( 'Plant', 'Post Type Singular Name', 'plant-guide' ),
		'menu_name'             => __( 'Plants', 'plant-guide' ),
		'name_admin_bar'        => __( 'Plant', 'plant-guide' ),
		'archives'              => __( 'Plant Archives', 'plant-guide' ),
		'attributes'            => __( 'Plant Attributes', 'plant-guide' ),
		'parent_item_colon'     => __( 'Parent Plant:', 'plant-guide' ),
		'all_items'             => __( 'All Plants', 'plant-guide' ),
		'add_new_item'          => __( 'Add New Plant', 'plant-guide' ),
		'add_new'               => __( 'Add New', 'plant-guide' ),
		'new_item'              => __( 'New Plant', 'plant-guide' ),
		'edit_item'             => __( 'Edit Plant', 'plant-guide' ),
		'update_item'           => __( 'Update Plant', 'plant-guide' ),
		'view_item'             => __( 'View Plant', 'plant-guide' ),
		'view_items'            => __( 'View Plants', 'plant-guide' ),
		'search_items'          => __( 'Search Plants', 'plant-guide' ),
		'not_found'             => __( 'Not found', 'plant-guide' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'plant-guide' ),
		'featured_image'        => __( 'Featured Image', 'plant-guide' ),
		'set_featured_image'    => __( 'Set featured image', 'plant-guide' ),
		'remove_featured_image' => __( 'Remove featured image', 'plant-guide' ),
		'use_featured_image'    => __( 'Use as featured image', 'plant-guide' ),
		'insert_into_item'      => __( 'Insert into plant', 'plant-guide' ),
		'uploaded_to_this_item' => __( 'Uploaded to this plant', 'plant-guide' ),
		'items_list'            => __( 'Plants list', 'plant-guide' ),
		'items_list_navigation' => __( 'Plants list navigation', 'plant-guide' ),
		'filter_items_list'     => __( 'Filter plants list', 'plant-guide' ),
	);

	$args = array(
		'label'               => __( 'Plant', 'plant-guide' ),
		'description'         => __( 'Plant species with care-level metadata', 'plant-guide' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-leaf',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'pgui_plant', $args );
}
add_action( 'init', 'pgui_register_post_types' );
