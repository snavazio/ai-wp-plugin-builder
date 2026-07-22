<?php
/**
 * Register custom post type for Works.
 *
 * @package Pskl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the pskl_work custom post type.
 *
 * @return void
 */
function pskl_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Works', 'Post Type General Name', 'portfolio-skills' ),
		'singular_name'         => _x( 'Work', 'Post Type Singular Name', 'portfolio-skills' ),
		'menu_name'             => __( 'Works', 'portfolio-skills' ),
		'name_admin_bar'        => __( 'Work', 'portfolio-skills' ),
		'archives'              => __( 'Work Archives', 'portfolio-skills' ),
		'attributes'            => __( 'Work Attributes', 'portfolio-skills' ),
		'parent_item_colon'     => __( 'Parent Work:', 'portfolio-skills' ),
		'all_items'             => __( 'All Works', 'portfolio-skills' ),
		'add_new_item'          => __( 'Add New Work', 'portfolio-skills' ),
		'add_new'               => __( 'Add New', 'portfolio-skills' ),
		'new_item'              => __( 'New Work', 'portfolio-skills' ),
		'edit_item'             => __( 'Edit Work', 'portfolio-skills' ),
		'update_item'           => __( 'Update Work', 'portfolio-skills' ),
		'view_item'             => __( 'View Work', 'portfolio-skills' ),
		'view_items'            => __( 'View Works', 'portfolio-skills' ),
		'search_items'          => __( 'Search Works', 'portfolio-skills' ),
		'not_found'             => __( 'Not found', 'portfolio-skills' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'portfolio-skills' ),
		'featured_image'        => __( 'Featured Image', 'portfolio-skills' ),
		'set_featured_image'    => __( 'Set featured image', 'portfolio-skills' ),
		'remove_featured_image' => __( 'Remove featured image', 'portfolio-skills' ),
		'use_featured_image'    => __( 'Use as featured image', 'portfolio-skills' ),
		'insert_into_item'      => __( 'Insert into work', 'portfolio-skills' ),
		'uploaded_to_this_item' => __( 'Uploaded to this work', 'portfolio-skills' ),
		'items_list'            => __( 'Works list', 'portfolio-skills' ),
		'items_list_navigation' => __( 'Works list navigation', 'portfolio-skills' ),
		'filter_items_list'     => __( 'Filter works list', 'portfolio-skills' ),
	);

	$args = array(
		'label'               => __( 'Work', 'portfolio-skills' ),
		'description'         => __( 'Portfolio works with skill tags', 'portfolio-skills' ),
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

	register_post_type( 'pskl_work', $args );
}
add_action( 'init', 'pskl_register_post_types' );
