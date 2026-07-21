<?php
/**
 * Register custom post types.
 *
 * @package Prtf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the prtf_portfolio custom post type.
 *
 * @return void
 */
function prtf_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Portfolios', 'Post Type General Name', 'portfolio' ),
		'singular_name'         => _x( 'Portfolio', 'Post Type Singular Name', 'portfolio' ),
		'menu_name'             => __( 'Portfolios', 'portfolio' ),
		'name_admin_bar'        => __( 'Portfolio', 'portfolio' ),
		'archives'              => __( 'Portfolio Archives', 'portfolio' ),
		'attributes'            => __( 'Portfolio Attributes', 'portfolio' ),
		'parent_item_colon'     => __( 'Parent Portfolio:', 'portfolio' ),
		'all_items'             => __( 'All Portfolios', 'portfolio' ),
		'add_new_item'          => __( 'Add New Portfolio', 'portfolio' ),
		'add_new'               => __( 'Add New', 'portfolio' ),
		'new_item'              => __( 'New Portfolio', 'portfolio' ),
		'edit_item'             => __( 'Edit Portfolio', 'portfolio' ),
		'update_item'           => __( 'Update Portfolio', 'portfolio' ),
		'view_item'             => __( 'View Portfolio', 'portfolio' ),
		'view_items'            => __( 'View Portfolios', 'portfolio' ),
		'search_items'          => __( 'Search Portfolio', 'portfolio' ),
		'not_found'             => __( 'Not found', 'portfolio' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'portfolio' ),
		'featured_image'        => __( 'Featured Image', 'portfolio' ),
		'set_featured_image'    => __( 'Set featured image', 'portfolio' ),
		'remove_featured_image' => __( 'Remove featured image', 'portfolio' ),
		'use_featured_image'    => __( 'Use as featured image', 'portfolio' ),
		'insert_into_item'      => __( 'Insert into portfolio', 'portfolio' ),
		'uploaded_to_this_item' => __( 'Uploaded to this portfolio', 'portfolio' ),
		'items_list'            => __( 'Portfolios list', 'portfolio' ),
		'items_list_navigation' => __( 'Portfolios list navigation', 'portfolio' ),
		'filter_items_list'     => __( 'Filter portfolios list', 'portfolio' ),
	);

	$args = array(
		'label'               => __( 'Portfolio', 'portfolio' ),
		'description'         => __( 'Agency portfolio projects', 'portfolio' ),
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

	register_post_type( 'prtf_portfolio', $args );
}
add_action( 'init', 'prtf_register_post_types' );
