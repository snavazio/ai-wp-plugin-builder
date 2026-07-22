<?php
/**
 * Register Event custom post type.
 *
 * @package Evcal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the event custom post type.
 *
 * @return void
 */
function evcal_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Events', 'Post Type General Name', 'events-calendar' ),
		'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'events-calendar' ),
		'menu_name'             => __( 'Events', 'events-calendar' ),
		'name_admin_bar'        => __( 'Event', 'events-calendar' ),
		'archives'              => __( 'Event Archives', 'events-calendar' ),
		'attributes'            => __( 'Event Attributes', 'events-calendar' ),
		'parent_item_colon'     => __( 'Parent Event:', 'events-calendar' ),
		'all_items'             => __( 'All Events', 'events-calendar' ),
		'add_new_item'          => __( 'Add New Event', 'events-calendar' ),
		'add_new'               => __( 'Add New', 'events-calendar' ),
		'new_item'              => __( 'New Event', 'events-calendar' ),
		'edit_item'             => __( 'Edit Event', 'events-calendar' ),
		'update_item'           => __( 'Update Event', 'events-calendar' ),
		'view_item'             => __( 'View Event', 'events-calendar' ),
		'view_items'            => __( 'View Events', 'events-calendar' ),
		'search_items'          => __( 'Search Events', 'events-calendar' ),
		'not_found'             => __( 'Not found', 'events-calendar' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'events-calendar' ),
		'featured_image'        => __( 'Featured Image', 'events-calendar' ),
		'set_featured_image'    => __( 'Set featured image', 'events-calendar' ),
		'remove_featured_image' => __( 'Remove featured image', 'events-calendar' ),
		'use_featured_image'    => __( 'Use as featured image', 'events-calendar' ),
		'insert_into_item'      => __( 'Insert into event', 'events-calendar' ),
		'uploaded_to_this_item' => __( 'Uploaded to this event', 'events-calendar' ),
		'items_list'            => __( 'Events list', 'events-calendar' ),
		'items_list_navigation' => __( 'Events list navigation', 'events-calendar' ),
		'filter_items_list'     => __( 'Filter events list', 'events-calendar' ),
	);

	$args = array(
		'label'               => __( 'Event', 'events-calendar' ),
		'description'         => __( 'Events with start date and location', 'events-calendar' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-calendar-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'event', $args );
}
add_action( 'init', 'evcal_register_post_types' );
