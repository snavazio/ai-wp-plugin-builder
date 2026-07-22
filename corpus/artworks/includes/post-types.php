<?php
/**
 * Register Artworks custom post type.
 *
 * @package Artw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the artwork custom post type.
 *
 * @return void
 */
function artw_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Artworks', 'Post Type General Name', 'artworks' ),
		'singular_name'         => _x( 'Artwork', 'Post Type Singular Name', 'artworks' ),
		'menu_name'             => __( 'Artworks', 'artworks' ),
		'name_admin_bar'        => __( 'Artwork', 'artworks' ),
		'archives'              => __( 'Artwork Archives', 'artworks' ),
		'attributes'            => __( 'Artwork Attributes', 'artworks' ),
		'parent_item_colon'     => __( 'Parent Artwork:', 'artworks' ),
		'all_items'             => __( 'All Artworks', 'artworks' ),
		'add_new_item'          => __( 'Add New Artwork', 'artworks' ),
		'add_new'               => __( 'Add New', 'artworks' ),
		'new_item'              => __( 'New Artwork', 'artworks' ),
		'edit_item'             => __( 'Edit Artwork', 'artworks' ),
		'update_item'           => __( 'Update Artwork', 'artworks' ),
		'view_item'             => __( 'View Artwork', 'artworks' ),
		'view_items'            => __( 'View Artworks', 'artworks' ),
		'search_items'          => __( 'Search Artworks', 'artworks' ),
		'not_found'             => __( 'Not found', 'artworks' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'artworks' ),
		'featured_image'        => __( 'Featured Image', 'artworks' ),
		'set_featured_image'    => __( 'Set featured image', 'artworks' ),
		'remove_featured_image' => __( 'Remove featured image', 'artworks' ),
		'use_featured_image'    => __( 'Use as featured image', 'artworks' ),
		'insert_into_item'      => __( 'Insert into artwork', 'artworks' ),
		'uploaded_to_this_item' => __( 'Uploaded to this artwork', 'artworks' ),
		'items_list'            => __( 'Artworks list', 'artworks' ),
		'items_list_navigation' => __( 'Artworks list navigation', 'artworks' ),
		'filter_items_list'     => __( 'Filter artworks list', 'artworks' ),
	);

	$args = array(
		'label'               => __( 'Artwork', 'artworks' ),
		'description'         => __( 'Artworks with artist and medium meta', 'artworks' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-art',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'artw_artwork', $args );
}
add_action( 'init', 'artw_register_post_types' );
