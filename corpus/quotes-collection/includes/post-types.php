<?php
/**
 * Register Quotes Collection custom post type.
 *
 * @package Qcl1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the quote custom post type.
 *
 * @return void
 */
function qcl1_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Quotes', 'Post Type General Name', 'quotes-collection' ),
		'singular_name'         => _x( 'Quote', 'Post Type Singular Name', 'quotes-collection' ),
		'menu_name'             => __( 'Quotes', 'quotes-collection' ),
		'name_admin_bar'        => __( 'Quote', 'quotes-collection' ),
		'archives'              => __( 'Quote Archives', 'quotes-collection' ),
		'attributes'            => __( 'Quote Attributes', 'quotes-collection' ),
		'parent_item_colon'     => __( 'Parent Quote:', 'quotes-collection' ),
		'all_items'             => __( 'All Quotes', 'quotes-collection' ),
		'add_new_item'          => __( 'Add New Quote', 'quotes-collection' ),
		'add_new'               => __( 'Add New', 'quotes-collection' ),
		'new_item'              => __( 'New Quote', 'quotes-collection' ),
		'edit_item'             => __( 'Edit Quote', 'quotes-collection' ),
		'update_item'           => __( 'Update Quote', 'quotes-collection' ),
		'view_item'             => __( 'View Quote', 'quotes-collection' ),
		'view_items'            => __( 'View Quotes', 'quotes-collection' ),
		'search_items'          => __( 'Search Quotes', 'quotes-collection' ),
		'not_found'             => __( 'Not found', 'quotes-collection' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'quotes-collection' ),
		'featured_image'        => __( 'Featured Image', 'quotes-collection' ),
		'set_featured_image'    => __( 'Set featured image', 'quotes-collection' ),
		'remove_featured_image' => __( 'Remove featured image', 'quotes-collection' ),
		'use_featured_image'    => __( 'Use as featured image', 'quotes-collection' ),
		'insert_into_item'      => __( 'Insert into quote', 'quotes-collection' ),
		'uploaded_to_this_item' => __( 'Uploaded to this quote', 'quotes-collection' ),
		'items_list'            => __( 'Quotes list', 'quotes-collection' ),
		'items_list_navigation' => __( 'Quotes list navigation', 'quotes-collection' ),
		'filter_items_list'     => __( 'Filter quotes list', 'quotes-collection' ),
	);

	$args = array(
		'label'               => __( 'Quote', 'quotes-collection' ),
		'description'         => __( 'Quotes with author and source', 'quotes-collection' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-quote',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'quote', $args );
}
add_action( 'init', 'qcl1_register_post_types' );
