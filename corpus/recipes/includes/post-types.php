<?php
/**
 * Register Recipe custom post type.
 *
 * @package Rcpr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the recipe custom post type.
 *
 * @return void
 */
function rcpr_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Recipes', 'Post Type General Name', 'recipes' ),
		'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'recipes' ),
		'menu_name'             => __( 'Recipes', 'recipes' ),
		'name_admin_bar'        => __( 'Recipe', 'recipes' ),
		'archives'              => __( 'Recipe Archives', 'recipes' ),
		'attributes'            => __( 'Recipe Attributes', 'recipes' ),
		'parent_item_colon'     => __( 'Parent Recipe:', 'recipes' ),
		'all_items'             => __( 'All Recipes', 'recipes' ),
		'add_new_item'          => __( 'Add New Recipe', 'recipes' ),
		'add_new'               => __( 'Add New', 'recipes' ),
		'new_item'              => __( 'New Recipe', 'recipes' ),
		'edit_item'             => __( 'Edit Recipe', 'recipes' ),
		'update_item'           => __( 'Update Recipe', 'recipes' ),
		'view_item'             => __( 'View Recipe', 'recipes' ),
		'view_items'            => __( 'View Recipes', 'recipes' ),
		'search_items'          => __( 'Search Recipes', 'recipes' ),
		'not_found'             => __( 'Not found', 'recipes' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'recipes' ),
		'featured_image'        => __( 'Featured Image', 'recipes' ),
		'set_featured_image'    => __( 'Set featured image', 'recipes' ),
		'remove_featured_image' => __( 'Remove featured image', 'recipes' ),
		'use_featured_image'    => __( 'Use as featured image', 'recipes' ),
		'insert_into_item'      => __( 'Insert into recipe', 'recipes' ),
		'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'recipes' ),
		'items_list'            => __( 'Recipes list', 'recipes' ),
		'items_list_navigation' => __( 'Recipes list navigation', 'recipes' ),
		'filter_items_list'     => __( 'Filter recipes list', 'recipes' ),
	);

	$args = array(
		'label'               => __( 'Recipe', 'recipes' ),
		'description'         => __( 'Recipes with prep time and servings', 'recipes' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-recipe',
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
add_action( 'init', 'rcpr_register_post_types' );
