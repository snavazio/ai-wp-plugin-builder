<?php
/**
 * Register custom post type for Podcast Episodes.
 *
 * @package Podc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the episode custom post type.
 *
 * @return void
 */
function podc_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Episodes', 'Post Type General Name', 'podcast-episodes' ),
		'singular_name'         => _x( 'Episode', 'Post Type Singular Name', 'podcast-episodes' ),
		'menu_name'             => __( 'Episodes', 'podcast-episodes' ),
		'name_admin_bar'        => __( 'Episode', 'podcast-episodes' ),
		'archives'              => __( 'Episode Archives', 'podcast-episodes' ),
		'attributes'            => __( 'Episode Attributes', 'podcast-episodes' ),
		'parent_item_colon'     => __( 'Parent Episode:', 'podcast-episodes' ),
		'all_items'             => __( 'All Episodes', 'podcast-episodes' ),
		'add_new_item'          => __( 'Add New Episode', 'podcast-episodes' ),
		'add_new'               => __( 'Add New', 'podcast-episodes' ),
		'new_item'              => __( 'New Episode', 'podcast-episodes' ),
		'edit_item'             => __( 'Edit Episode', 'podcast-episodes' ),
		'update_item'           => __( 'Update Episode', 'podcast-episodes' ),
		'view_item'             => __( 'View Episode', 'podcast-episodes' ),
		'view_items'            => __( 'View Episodes', 'podcast-episodes' ),
		'search_items'          => __( 'Search Episodes', 'podcast-episodes' ),
		'not_found'             => __( 'Not found', 'podcast-episodes' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'podcast-episodes' ),
		'featured_image'        => __( 'Featured Image', 'podcast-episodes' ),
		'set_featured_image'    => __( 'Set featured image', 'podcast-episodes' ),
		'remove_featured_image' => __( 'Remove featured image', 'podcast-episodes' ),
		'use_featured_image'    => __( 'Use as featured image', 'podcast-episodes' ),
		'insert_into_item'      => __( 'Insert into episode', 'podcast-episodes' ),
		'uploaded_to_this_item' => __( 'Uploaded to this episode', 'podcast-episodes' ),
		'items_list'            => __( 'Episodes list', 'podcast-episodes' ),
		'items_list_navigation' => __( 'Episodes list navigation', 'podcast-episodes' ),
		'filter_items_list'     => __( 'Filter episodes list', 'podcast-episodes' ),
	);

	$args = array(
		'label'               => __( 'Episode', 'podcast-episodes' ),
		'description'         => __( 'Podcast episodes with audio URL, duration meta, and season taxonomy', 'podcast-episodes' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-microphone',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'episode', $args );
}
add_action( 'init', 'podc_register_post_types' );
