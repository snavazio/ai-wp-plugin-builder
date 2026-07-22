<?php
/**
 * Register Glossary Terms custom post type.
 *
 * @package Gterm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the gterm_glossary_term custom post type.
 *
 * @return void
 */
function gterm_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Glossary Terms', 'Post Type General Name', 'glossary-terms' ),
		'singular_name'         => _x( 'Glossary Term', 'Post Type Singular Name', 'glossary-terms' ),
		'menu_name'             => __( 'Glossary Terms', 'glossary-terms' ),
		'name_admin_bar'        => __( 'Glossary Term', 'glossary-terms' ),
		'archives'              => __( 'Glossary Term Archives', 'glossary-terms' ),
		'attributes'            => __( 'Glossary Term Attributes', 'glossary-terms' ),
		'parent_item_colon'     => __( 'Parent Glossary Term:', 'glossary-terms' ),
		'all_items'             => __( 'All Glossary Terms', 'glossary-terms' ),
		'add_new_item'          => __( 'Add New Glossary Term', 'glossary-terms' ),
		'add_new'               => __( 'Add New', 'glossary-terms' ),
		'new_item'              => __( 'New Glossary Term', 'glossary-terms' ),
		'edit_item'             => __( 'Edit Glossary Term', 'glossary-terms' ),
		'update_item'           => __( 'Update Glossary Term', 'glossary-terms' ),
		'view_item'             => __( 'View Glossary Term', 'glossary-terms' ),
		'view_items'            => __( 'View Glossary Terms', 'glossary-terms' ),
		'search_items'          => __( 'Search Glossary Terms', 'glossary-terms' ),
		'not_found'             => __( 'Not found', 'glossary-terms' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'glossary-terms' ),
		'featured_image'        => __( 'Featured Image', 'glossary-terms' ),
		'set_featured_image'    => __( 'Set featured image', 'glossary-terms' ),
		'remove_featured_image' => __( 'Remove featured image', 'glossary-terms' ),
		'use_featured_image'    => __( 'Use as featured image', 'glossary-terms' ),
		'insert_into_item'      => __( 'Insert into glossary term', 'glossary-terms' ),
		'uploaded_to_this_item' => __( 'Uploaded to this glossary term', 'glossary-terms' ),
		'items_list'            => __( 'Glossary Terms list', 'glossary-terms' ),
		'items_list_navigation' => __( 'Glossary Terms list navigation', 'glossary-terms' ),
		'filter_items_list'     => __( 'Filter glossary terms list', 'glossary-terms' ),
	);

	$args = array(
		'label'               => __( 'Glossary Term', 'glossary-terms' ),
		'description'         => __( 'Glossary terms for the site', 'glossary-terms' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-book',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'gterm_glossary_term', $args );
}
add_action( 'init', 'gterm_register_post_types' );
