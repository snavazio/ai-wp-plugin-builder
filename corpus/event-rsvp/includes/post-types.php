<?php
/**
 * Register custom post types.
 *
 * @package Ersv
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the event custom post type.
 *
 * @return void
 */
function ersv_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Events', 'Post Type General Name', 'event-rsvp' ),
		'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'event-rsvp' ),
		'menu_name'             => __( 'Events', 'event-rsvp' ),
		'name_admin_bar'        => __( 'Event', 'event-rsvp' ),
		'archives'              => __( 'Event Archives', 'event-rsvp' ),
		'attributes'            => __( 'Event Attributes', 'event-rsvp' ),
		'parent_item_colon'     => __( 'Parent Event:', 'event-rsvp' ),
		'all_items'             => __( 'All Events', 'event-rsvp' ),
		'add_new_item'          => __( 'Add New Event', 'event-rsvp' ),
		'add_new'               => __( 'Add New', 'event-rsvp' ),
		'new_item'              => __( 'New Event', 'event-rsvp' ),
		'edit_item'             => __( 'Edit Event', 'event-rsvp' ),
		'update_item'           => __( 'Update Event', 'event-rsvp' ),
		'view_item'             => __( 'View Event', 'event-rsvp' ),
		'view_items'            => __( 'View Events', 'event-rsvp' ),
		'search_items'          => __( 'Search Events', 'event-rsvp' ),
		'not_found'             => __( 'Not found', 'event-rsvp' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'event-rsvp' ),
		'featured_image'        => __( 'Featured Image', 'event-rsvp' ),
		'set_featured_image'    => __( 'Set featured image', 'event-rsvp' ),
		'remove_featured_image' => __( 'Remove featured image', 'event-rsvp' ),
		'use_featured_image'    => __( 'Use as featured image', 'event-rsvp' ),
		'insert_into_item'      => __( 'Insert into event', 'event-rsvp' ),
		'uploaded_to_this_item' => __( 'Uploaded to this event', 'event-rsvp' ),
		'items_list'            => __( 'Events list', 'event-rsvp' ),
		'items_list_navigation' => __( 'Events list navigation', 'event-rsvp' ),
		'filter_items_list'     => __( 'Filter events list', 'event-rsvp' ),
	);

	$args = array(
		'label'               => __( 'Event', 'event-rsvp' ),
		'description'         => __( 'Events with RSVP functionality', 'event-rsvp' ),
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
add_action( 'init', 'ersv_register_post_types' );
