<?php
/**
 * Register custom post type for Games.
 *
 * @package Gcat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the game custom post type.
 *
 * @return void
 */
function gcat_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Games', 'Post Type General Name', 'game-catalog' ),
		'singular_name'         => _x( 'Game', 'Post Type Singular Name', 'game-catalog' ),
		'menu_name'             => __( 'Games', 'game-catalog' ),
		'name_admin_bar'        => __( 'Game', 'game-catalog' ),
		'archives'              => __( 'Game Archives', 'game-catalog' ),
		'attributes'            => __( 'Game Attributes', 'game-catalog' ),
		'parent_item_colon'     => __( 'Parent Game:', 'game-catalog' ),
		'all_items'             => __( 'All Games', 'game-catalog' ),
		'add_new_item'          => __( 'Add New Game', 'game-catalog' ),
		'add_new'               => __( 'Add New', 'game-catalog' ),
		'new_item'              => __( 'New Game', 'game-catalog' ),
		'edit_item'             => __( 'Edit Game', 'game-catalog' ),
		'update_item'           => __( 'Update Game', 'game-catalog' ),
		'view_item'             => __( 'View Game', 'game-catalog' ),
		'view_items'            => __( 'View Games', 'game-catalog' ),
		'search_items'          => __( 'Search Games', 'game-catalog' ),
		'not_found'             => __( 'Not found', 'game-catalog' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'game-catalog' ),
		'featured_image'        => __( 'Featured Image', 'game-catalog' ),
		'set_featured_image'    => __( 'Set featured image', 'game-catalog' ),
		'remove_featured_image' => __( 'Remove featured image', 'game-catalog' ),
		'use_featured_image'    => __( 'Use as featured image', 'game-catalog' ),
		'insert_into_item'      => __( 'Insert into game', 'game-catalog' ),
		'uploaded_to_this_item' => __( 'Uploaded to this game', 'game-catalog' ),
		'items_list'            => __( 'Games list', 'game-catalog' ),
		'items_list_navigation' => __( 'Games list navigation', 'game-catalog' ),
		'filter_items_list'     => __( 'Filter games list', 'game-catalog' ),
	);

	$args = array(
		'label'               => __( 'Game', 'game-catalog' ),
		'description'         => __( 'Games with release year, platform, and genre', 'game-catalog' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-video-alt3',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'game', $args );
}
add_action( 'init', 'gcat_register_post_types' );
