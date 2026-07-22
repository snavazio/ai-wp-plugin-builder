<?php
/**
 * Register Workshop custom post type.
 *
 * @package Wcpm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the workshop custom post type.
 *
 * @return void
 */
function wcpm_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Workshops', 'Post Type General Name', 'workshop-cpt' ),
		'singular_name'         => _x( 'Workshop', 'Post Type Singular Name', 'workshop-cpt' ),
		'menu_name'             => __( 'Workshops', 'workshop-cpt' ),
		'name_admin_bar'        => __( 'Workshop', 'workshop-cpt' ),
		'archives'              => __( 'Workshop Archives', 'workshop-cpt' ),
		'attributes'            => __( 'Workshop Attributes', 'workshop-cpt' ),
		'parent_item_colon'     => __( 'Parent Workshop:', 'workshop-cpt' ),
		'all_items'             => __( 'All Workshops', 'workshop-cpt' ),
		'add_new_item'          => __( 'Add New Workshop', 'workshop-cpt' ),
		'add_new'               => __( 'Add New', 'workshop-cpt' ),
		'new_item'              => __( 'New Workshop', 'workshop-cpt' ),
		'edit_item'             => __( 'Edit Workshop', 'workshop-cpt' ),
		'update_item'           => __( 'Update Workshop', 'workshop-cpt' ),
		'view_item'             => __( 'View Workshop', 'workshop-cpt' ),
		'view_items'            => __( 'View Workshops', 'workshop-cpt' ),
		'search_items'          => __( 'Search Workshops', 'workshop-cpt' ),
		'not_found'             => __( 'Not found', 'workshop-cpt' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'workshop-cpt' ),
		'featured_image'        => __( 'Featured Image', 'workshop-cpt' ),
		'set_featured_image'    => __( 'Set featured image', 'workshop-cpt' ),
		'remove_featured_image' => __( 'Remove featured image', 'workshop-cpt' ),
		'use_featured_image'    => __( 'Use as featured image', 'workshop-cpt' ),
		'insert_into_item'      => __( 'Insert into workshop', 'workshop-cpt' ),
		'uploaded_to_this_item' => __( 'Uploaded to this workshop', 'workshop-cpt' ),
		'items_list'            => __( 'Workshops list', 'workshop-cpt' ),
		'items_list_navigation' => __( 'Workshops list navigation', 'workshop-cpt' ),
		'filter_items_list'     => __( 'Filter workshops list', 'workshop-cpt' ),
	);

	$args = array(
		'label'               => __( 'Workshop', 'workshop-cpt' ),
		'description'         => __( 'Workshops with date and seats', 'workshop-cpt' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-calendar-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'workshop', $args );
}
add_action( 'init', 'wcpm_register_post_types' );
