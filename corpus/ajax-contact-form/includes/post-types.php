<?php
/**
 * Register custom post types.
 *
 * @package Acff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the acff_submission custom post type.
 *
 * @return void
 */
function acff_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Contact Submissions', 'Post Type General Name', 'ajax-contact-form' ),
		'singular_name'         => _x( 'Contact Submission', 'Post Type Singular Name', 'ajax-contact-form' ),
		'menu_name'             => __( 'Contact Submissions', 'ajax-contact-form' ),
		'name_admin_bar'        => __( 'Contact Submission', 'ajax-contact-form' ),
		'archives'              => __( 'Submission Archives', 'ajax-contact-form' ),
		'attributes'            => __( 'Submission Attributes', 'ajax-contact-form' ),
		'parent_item_colon'     => __( 'Parent Submission:', 'ajax-contact-form' ),
		'all_items'             => __( 'All Submissions', 'ajax-contact-form' ),
		'add_new_item'          => __( 'Add New Submission', 'ajax-contact-form' ),
		'add_new'               => __( 'Add New', 'ajax-contact-form' ),
		'new_item'              => __( 'New Submission', 'ajax-contact-form' ),
		'edit_item'             => __( 'Edit Submission', 'ajax-contact-form' ),
		'update_item'           => __( 'Update Submission', 'ajax-contact-form' ),
		'view_item'             => __( 'View Submission', 'ajax-contact-form' ),
		'view_items'            => __( 'View Submissions', 'ajax-contact-form' ),
		'search_items'          => __( 'Search Submissions', 'ajax-contact-form' ),
		'not_found'             => __( 'Not found', 'ajax-contact-form' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'ajax-contact-form' ),
		'featured_image'        => __( 'Featured Image', 'ajax-contact-form' ),
		'set_featured_image'    => __( 'Set featured image', 'ajax-contact-form' ),
		'remove_featured_image' => __( 'Remove featured image', 'ajax-contact-form' ),
		'use_featured_image'    => __( 'Use as featured image', 'ajax-contact-form' ),
		'insert_into_item'      => __( 'Insert into submission', 'ajax-contact-form' ),
		'uploaded_to_this_item' => __( 'Uploaded to this submission', 'ajax-contact-form' ),
		'items_list'            => __( 'Submissions list', 'ajax-contact-form' ),
		'items_list_navigation' => __( 'Submissions list navigation', 'ajax-contact-form' ),
		'filter_items_list'     => __( 'Filter submissions list', 'ajax-contact-form' ),
	);

	$args = array(
		'label'               => __( 'Contact Submission', 'ajax-contact-form' ),
		'description'         => __( 'Contact form submissions', 'ajax-contact-form' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 25,
		'menu_icon'           => 'dashicons-email',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'acff_submission', $args );
}
add_action( 'init', 'acff_register_post_types' );
