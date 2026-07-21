<?php
/**
 * Register testimonial custom post type.
 *
 * @package Tmnl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the tmnl_testimonial post type.
 *
 * @return void
 */
function tmnl_register_post_type() {
	$labels = array(
		'name'                  => _x( 'Testimonials', 'Post type general name', 'testimonials' ),
		'singular_name'         => _x( 'Testimonial', 'Post type singular name', 'testimonials' ),
		'menu_name'             => _x( 'Testimonials', 'Admin Menu text', 'testimonials' ),
		'name_admin_bar'        => _x( 'Testimonial', 'Add New on Toolbar', 'testimonials' ),
		'add_new'               => __( 'Add New', 'testimonials' ),
		'add_new_item'          => __( 'Add New Testimonial', 'testimonials' ),
		'new_item'              => __( 'New Testimonial', 'testimonials' ),
		'edit_item'             => __( 'Edit Testimonial', 'testimonials' ),
		'view_item'             => __( 'View Testimonial', 'testimonials' ),
		'all_items'             => __( 'All Testimonials', 'testimonials' ),
		'search_items'          => __( 'Search Testimonials', 'testimonials' ),
		'parent_item_colon'     => __( 'Parent Testimonials:', 'testimonials' ),
		'not_found'             => __( 'No testimonials found.', 'testimonials' ),
		'not_found_in_trash'    => __( 'No testimonials found in Trash.', 'testimonials' ),
		'archives'              => _x( 'Testimonial archives', 'The post type archive label used in nav menus.', 'testimonials' ),
		'insert_into_item'      => _x( 'Insert into testimonial', 'Overrides the "Insert into post"/"Insert into page" phrase.', 'testimonials' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this testimonial', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase.', 'testimonials' ),
		'filter_items_list'     => _x( 'Filter testimonials list', 'Screen reader text for the filter links heading on the post type listing screen.', 'testimonials' ),
		'items_list_navigation' => _x( 'Testimonials list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'testimonials' ),
		'items_list'            => _x( 'Testimonials list', 'Screen reader text for the items list heading on the post type listing screen.', 'testimonials' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-testimonial',
		'supports'           => array( 'title', 'editor' ),
		'show_in_rest'       => false,
	);

	register_post_type( 'tmnl_testimonial', $args );
}
add_action( 'init', 'tmnl_register_post_type' );
