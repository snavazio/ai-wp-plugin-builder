<?php
/**
 * Register custom post type for Recipes.
 *
 * @package Cook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the recipe custom post type.
 *
 * @return void
 */
function cook_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Recipes', 'Post Type General Name', 'cookbook' ),
		'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'cookbook' ),
		'menu_name'             => __( 'Recipes', 'cookbook' ),
		'name_admin_bar'        => __( 'Recipe', 'cookbook' ),
		'archives'              => __( 'Recipe Archives', 'cookbook' ),
		'attributes'            => __( 'Recipe Attributes', 'cookbook' ),
		'parent_item_colon'     => __( 'Parent Recipe:', 'cookbook' ),
		'all_items'             => __( 'All Recipes', 'cookbook' ),
		'add_new_item'          => __( 'Add New Recipe', 'cookbook' ),
		'add_new'               => __( 'Add New', 'cookbook' ),
		'new_item'              => __( 'New Recipe', 'cookbook' ),
		'edit_item'             => __( 'Edit Recipe', 'cookbook' ),
		'update_item'           => __( 'Update Recipe', 'cookbook' ),
		'view_item'             => __( 'View Recipe', 'cookbook' ),
		'view_items'            => __( 'View Recipes', 'cookbook' ),
		'search_items'          => __( 'Search Recipes', 'cookbook' ),
		'not_found'             => __( 'Not found', 'cookbook' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'cookbook' ),
		'featured_image'        => __( 'Featured Image', 'cookbook' ),
		'set_featured_image'    => __( 'Set featured image', 'cookbook' ),
		'remove_featured_image' => __( 'Remove featured image', 'cookbook' ),
		'use_featured_image'    => __( 'Use as featured image', 'cookbook' ),
		'insert_into_item'      => __( 'Insert into recipe', 'cookbook' ),
		'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'cookbook' ),
		'items_list'            => __( 'Recipes list', 'cookbook' ),
		'items_list_navigation' => __( 'Recipes list navigation', 'cookbook' ),
		'filter_items_list'     => __( 'Filter recipes list', 'cookbook' ),
	);

	$args = array(
		'label'               => __( 'Recipe', 'cookbook' ),
		'description'         => __( 'Recipes with Course and Cuisine taxonomies', 'cookbook' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-restaurant',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'recipe', $args );
}
add_action( 'init', 'cook_register_post_types' );
