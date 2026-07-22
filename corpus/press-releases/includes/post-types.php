<?php
/**
 * Register Press Release custom post type.
 *
 * @package Prcs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the press_release custom post type.
 *
 * @return void
 */
function prcs_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Press Releases', 'Post Type General Name', 'press-releases' ),
		'singular_name'         => _x( 'Press Release', 'Post Type Singular Name', 'press-releases' ),
		'menu_name'             => __( 'Press Releases', 'press-releases' ),
		'name_admin_bar'        => __( 'Press Release', 'press-releases' ),
		'archives'              => __( 'Press Release Archives', 'press-releases' ),
		'attributes'            => __( 'Press Release Attributes', 'press-releases' ),
		'parent_item_colon'     => __( 'Parent Press Release:', 'press-releases' ),
		'all_items'             => __( 'All Press Releases', 'press-releases' ),
		'add_new_item'          => __( 'Add New Press Release', 'press-releases' ),
		'add_new'               => __( 'Add New', 'press-releases' ),
		'new_item'              => __( 'New Press Release', 'press-releases' ),
		'edit_item'             => __( 'Edit Press Release', 'press-releases' ),
		'update_item'           => __( 'Update Press Release', 'press-releases' ),
		'view_item'             => __( 'View Press Release', 'press-releases' ),
		'view_items'            => __( 'View Press Releases', 'press-releases' ),
		'search_items'          => __( 'Search Press Releases', 'press-releases' ),
		'not_found'             => __( 'Not found', 'press-releases' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'press-releases' ),
		'featured_image'        => __( 'Featured Image', 'press-releases' ),
		'set_featured_image'    => __( 'Set featured image', 'press-releases' ),
		'remove_featured_image' => __( 'Remove featured image', 'press-releases' ),
		'use_featured_image'    => __( 'Use as featured image', 'press-releases' ),
		'insert_into_item'      => __( 'Insert into press release', 'press-releases' ),
		'uploaded_to_this_item' => __( 'Uploaded to this press release', 'press-releases' ),
		'items_list'            => __( 'Press Releases list', 'press-releases' ),
		'items_list_navigation' => __( 'Press Releases list navigation', 'press-releases' ),
		'filter_items_list'     => __( 'Filter press releases list', 'press-releases' ),
	);

	$args = array(
		'label'               => __( 'Press Release', 'press-releases' ),
		'description'         => __( 'Press releases with date meta', 'press-releases' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-media-document',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'press_release', $args );
}
add_action( 'init', 'prcs_register_post_types' );
