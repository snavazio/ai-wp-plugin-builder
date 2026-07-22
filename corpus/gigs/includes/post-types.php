<?php
/**
 * Register Gigs custom post type.
 *
 * @package Gigc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the gigc_gig custom post type.
 *
 * @return void
 */
function gigc_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Gigs', 'Post Type General Name', 'gigs' ),
		'singular_name'         => _x( 'Gig', 'Post Type Singular Name', 'gigs' ),
		'menu_name'             => __( 'Gigs', 'gigs' ),
		'name_admin_bar'        => __( 'Gig', 'gigs' ),
		'archives'              => __( 'Gig Archives', 'gigs' ),
		'attributes'            => __( 'Gig Attributes', 'gigs' ),
		'parent_item_colon'     => __( 'Parent Gig:', 'gigs' ),
		'all_items'             => __( 'All Gigs', 'gigs' ),
		'add_new_item'          => __( 'Add New Gig', 'gigs' ),
		'add_new'               => __( 'Add New', 'gigs' ),
		'new_item'              => __( 'New Gig', 'gigs' ),
		'edit_item'             => __( 'Edit Gig', 'gigs' ),
		'update_item'           => __( 'Update Gig', 'gigs' ),
		'view_item'             => __( 'View Gig', 'gigs' ),
		'view_items'            => __( 'View Gigs', 'gigs' ),
		'search_items'          => __( 'Search Gigs', 'gigs' ),
		'not_found'             => __( 'Not found', 'gigs' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'gigs' ),
		'featured_image'        => __( 'Featured Image', 'gigs' ),
		'set_featured_image'    => __( 'Set featured image', 'gigs' ),
		'remove_featured_image' => __( 'Remove featured image', 'gigs' ),
		'use_featured_image'    => __( 'Use as featured image', 'gigs' ),
		'insert_into_item'      => __( 'Insert into gig', 'gigs' ),
		'uploaded_to_this_item' => __( 'Uploaded to this gig', 'gigs' ),
		'items_list'            => __( 'Gigs list', 'gigs' ),
		'items_list_navigation' => __( 'Gigs list navigation', 'gigs' ),
		'filter_items_list'     => __( 'Filter gigs list', 'gigs' ),
	);

	$args = array(
		'label'               => __( 'Gig', 'gigs' ),
		'description'         => __( 'Gigs with venue and date', 'gigs' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-calendar-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'gigc_gig', $args );
}
add_action( 'init', 'gigc_register_post_types' );
