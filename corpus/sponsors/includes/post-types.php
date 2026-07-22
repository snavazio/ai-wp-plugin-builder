<?php
/**
 * Register Sponsors custom post type.
 *
 * @package Spns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the spns_sponsor custom post type.
 *
 * @return void
 */
function spns_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Sponsors', 'Post Type General Name', 'sponsors' ),
		'singular_name'         => _x( 'Sponsor', 'Post Type Singular Name', 'sponsors' ),
		'menu_name'             => __( 'Sponsors', 'sponsors' ),
		'name_admin_bar'        => __( 'Sponsor', 'sponsors' ),
		'archives'              => __( 'Sponsor Archives', 'sponsors' ),
		'attributes'            => __( 'Sponsor Attributes', 'sponsors' ),
		'parent_item_colon'     => __( 'Parent Sponsor:', 'sponsors' ),
		'all_items'             => __( 'All Sponsors', 'sponsors' ),
		'add_new_item'          => __( 'Add New Sponsor', 'sponsors' ),
		'add_new'               => __( 'Add New', 'sponsors' ),
		'new_item'              => __( 'New Sponsor', 'sponsors' ),
		'edit_item'             => __( 'Edit Sponsor', 'sponsors' ),
		'update_item'           => __( 'Update Sponsor', 'sponsors' ),
		'view_item'             => __( 'View Sponsor', 'sponsors' ),
		'view_items'            => __( 'View Sponsors', 'sponsors' ),
		'search_items'          => __( 'Search Sponsors', 'sponsors' ),
		'not_found'             => __( 'Not found', 'sponsors' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'sponsors' ),
		'featured_image'        => __( 'Featured Image', 'sponsors' ),
		'set_featured_image'    => __( 'Set featured image', 'sponsors' ),
		'remove_featured_image' => __( 'Remove featured image', 'sponsors' ),
		'use_featured_image'    => __( 'Use as featured image', 'sponsors' ),
		'insert_into_item'      => __( 'Insert into sponsor', 'sponsors' ),
		'uploaded_to_this_item' => __( 'Uploaded to this sponsor', 'sponsors' ),
		'items_list'            => __( 'Sponsors list', 'sponsors' ),
		'items_list_navigation' => __( 'Sponsors list navigation', 'sponsors' ),
		'filter_items_list'     => __( 'Filter sponsors list', 'sponsors' ),
	);

	$args = array(
		'label'               => __( 'Sponsor', 'sponsors' ),
		'description'         => __( 'Sponsor information', 'sponsors' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-groups',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'sponsors' ),
	);

	register_post_type( 'spns_sponsor', $args );
}
add_action( 'init', 'spns_register_post_types' );
