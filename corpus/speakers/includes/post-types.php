<?php
/**
 * Register Speaker custom post type.
 *
 * @package Spkrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the speaker custom post type.
 *
 * @return void
 */
function spkrs_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Speakers', 'Post Type General Name', 'speakers' ),
		'singular_name'         => _x( 'Speaker', 'Post Type Singular Name', 'speakers' ),
		'menu_name'             => __( 'Speakers', 'speakers' ),
		'name_admin_bar'        => __( 'Speaker', 'speakers' ),
		'archives'              => __( 'Speaker Archives', 'speakers' ),
		'attributes'            => __( 'Speaker Attributes', 'speakers' ),
		'parent_item_colon'     => __( 'Parent Speaker:', 'speakers' ),
		'all_items'             => __( 'All Speakers', 'speakers' ),
		'add_new_item'          => __( 'Add New Speaker', 'speakers' ),
		'add_new'               => __( 'Add New', 'speakers' ),
		'new_item'              => __( 'New Speaker', 'speakers' ),
		'edit_item'             => __( 'Edit Speaker', 'speakers' ),
		'update_item'           => __( 'Update Speaker', 'speakers' ),
		'view_item'             => __( 'View Speaker', 'speakers' ),
		'view_items'            => __( 'View Speakers', 'speakers' ),
		'search_items'          => __( 'Search Speakers', 'speakers' ),
		'not_found'             => __( 'Not found', 'speakers' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'speakers' ),
		'featured_image'        => __( 'Featured Image', 'speakers' ),
		'set_featured_image'    => __( 'Set featured image', 'speakers' ),
		'remove_featured_image' => __( 'Remove featured image', 'speakers' ),
		'use_featured_image'    => __( 'Use as featured image', 'speakers' ),
		'insert_into_item'      => __( 'Insert into speaker', 'speakers' ),
		'uploaded_to_this_item' => __( 'Uploaded to this speaker', 'speakers' ),
		'items_list'            => __( 'Speakers list', 'speakers' ),
		'items_list_navigation' => __( 'Speakers list navigation', 'speakers' ),
		'filter_items_list'     => __( 'Filter speakers list', 'speakers' ),
	);

	$args = array(
		'label'               => __( 'Speaker', 'speakers' ),
		'description'         => __( 'Speakers with Twitter handle', 'speakers' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-twitter',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'speaker', $args );
}
add_action( 'init', 'spkrs_register_post_types' );
