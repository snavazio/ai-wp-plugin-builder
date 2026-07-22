<?php
/**
 * Register custom post types.
 *
 * @package Bkslot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the slot custom post type.
 *
 * @return void
 */
function bkslot_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Slots', 'Post Type General Name', 'booking-slots' ),
		'singular_name'         => _x( 'Slot', 'Post Type Singular Name', 'booking-slots' ),
		'menu_name'             => __( 'Slots', 'booking-slots' ),
		'name_admin_bar'        => __( 'Slot', 'booking-slots' ),
		'archives'              => __( 'Slot Archives', 'booking-slots' ),
		'attributes'            => __( 'Slot Attributes', 'booking-slots' ),
		'parent_item_colon'     => __( 'Parent Slot:', 'booking-slots' ),
		'all_items'             => __( 'All Slots', 'booking-slots' ),
		'add_new_item'          => __( 'Add New Slot', 'booking-slots' ),
		'add_new'               => __( 'Add New', 'booking-slots' ),
		'new_item'              => __( 'New Slot', 'booking-slots' ),
		'edit_item'             => __( 'Edit Slot', 'booking-slots' ),
		'update_item'           => __( 'Update Slot', 'booking-slots' ),
		'view_item'             => __( 'View Slot', 'booking-slots' ),
		'view_items'            => __( 'View Slots', 'booking-slots' ),
		'search_items'          => __( 'Search Slots', 'booking-slots' ),
		'not_found'             => __( 'Not found', 'booking-slots' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'booking-slots' ),
		'featured_image'        => __( 'Featured Image', 'booking-slots' ),
		'set_featured_image'    => __( 'Set featured image', 'booking-slots' ),
		'remove_featured_image' => __( 'Remove featured image', 'booking-slots' ),
		'use_featured_image'    => __( 'Use as featured image', 'booking-slots' ),
		'insert_into_item'      => __( 'Insert into slot', 'booking-slots' ),
		'uploaded_to_this_item' => __( 'Uploaded to this slot', 'booking-slots' ),
		'items_list'            => __( 'Slots list', 'booking-slots' ),
		'items_list_navigation' => __( 'Slots list navigation', 'booking-slots' ),
		'filter_items_list'     => __( 'Filter slots list', 'booking-slots' ),
	);

	$args = array(
		'label'               => __( 'Slot', 'booking-slots' ),
		'description'         => __( 'Booking slots with datetime meta', 'booking-slots' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
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
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'slot', $args );
}
add_action( 'init', 'bkslot_register_post_types' );
