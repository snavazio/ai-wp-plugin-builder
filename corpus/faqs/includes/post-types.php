<?php
/**
 * Register FAQ custom post type.
 *
 * @package Faqs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the faqs_faq custom post type.
 *
 * @return void
 */
function faqs_register_post_types() {
	$labels = array(
		'name'                  => _x( 'FAQs', 'Post Type General Name', 'faqs' ),
		'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'faqs' ),
		'menu_name'             => __( 'FAQs', 'faqs' ),
		'name_admin_bar'        => __( 'FAQ', 'faqs' ),
		'archives'              => __( 'FAQ Archives', 'faqs' ),
		'attributes'            => __( 'FAQ Attributes', 'faqs' ),
		'parent_item_colon'     => __( 'Parent FAQ:', 'faqs' ),
		'all_items'             => __( 'All FAQs', 'faqs' ),
		'add_new_item'          => __( 'Add New FAQ', 'faqs' ),
		'add_new'               => __( 'Add New', 'faqs' ),
		'new_item'              => __( 'New FAQ', 'faqs' ),
		'edit_item'             => __( 'Edit FAQ', 'faqs' ),
		'update_item'           => __( 'Update FAQ', 'faqs' ),
		'view_item'             => __( 'View FAQ', 'faqs' ),
		'view_items'            => __( 'View FAQs', 'faqs' ),
		'search_items'          => __( 'Search FAQs', 'faqs' ),
		'not_found'             => __( 'Not found', 'faqs' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'faqs' ),
		'featured_image'        => __( 'Featured Image', 'faqs' ),
		'set_featured_image'    => __( 'Set featured image', 'faqs' ),
		'remove_featured_image' => __( 'Remove featured image', 'faqs' ),
		'use_featured_image'    => __( 'Use as featured image', 'faqs' ),
		'insert_into_item'      => __( 'Insert into FAQ', 'faqs' ),
		'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'faqs' ),
		'items_list'            => __( 'FAQs list', 'faqs' ),
		'items_list_navigation' => __( 'FAQs list navigation', 'faqs' ),
		'filter_items_list'     => __( 'Filter FAQs list', 'faqs' ),
	);

	$args = array(
		'label'               => __( 'FAQ', 'faqs' ),
		'description'         => __( 'Frequently Asked Questions', 'faqs' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-editor-help',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'faqs_faq', $args );
}
add_action( 'init', 'faqs_register_post_types' );
