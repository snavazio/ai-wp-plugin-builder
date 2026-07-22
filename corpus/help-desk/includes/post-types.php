<?php
/**
 * Register custom post type for Help Tickets.
 *
 * @package Help
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the help_ticket custom post type.
 *
 * @return void
 */
function help_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Help Tickets', 'Post Type General Name', 'help-desk' ),
		'singular_name'         => _x( 'Help Ticket', 'Post Type Singular Name', 'help-desk' ),
		'menu_name'             => __( 'Help Tickets', 'help-desk' ),
		'name_admin_bar'        => __( 'Help Ticket', 'help-desk' ),
		'archives'              => __( 'Help Ticket Archives', 'help-desk' ),
		'attributes'            => __( 'Help Ticket Attributes', 'help-desk' ),
		'parent_item_colon'     => __( 'Parent Help Ticket:', 'help-desk' ),
		'all_items'             => __( 'All Help Tickets', 'help-desk' ),
		'add_new_item'          => __( 'Add New Help Ticket', 'help-desk' ),
		'add_new'               => __( 'Add New', 'help-desk' ),
		'new_item'              => __( 'New Help Ticket', 'help-desk' ),
		'edit_item'             => __( 'Edit Help Ticket', 'help-desk' ),
		'update_item'           => __( 'Update Help Ticket', 'help-desk' ),
		'view_item'             => __( 'View Help Ticket', 'help-desk' ),
		'view_items'            => __( 'View Help Tickets', 'help-desk' ),
		'search_items'          => __( 'Search Help Tickets', 'help-desk' ),
		'not_found'             => __( 'Not found', 'help-desk' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'help-desk' ),
		'featured_image'        => __( 'Featured Image', 'help-desk' ),
		'set_featured_image'    => __( 'Set featured image', 'help-desk' ),
		'remove_featured_image' => __( 'Remove featured image', 'help-desk' ),
		'use_featured_image'    => __( 'Use as featured image', 'help-desk' ),
		'insert_into_item'      => __( 'Insert into help ticket', 'help-desk' ),
		'uploaded_to_this_item' => __( 'Uploaded to this help ticket', 'help-desk' ),
		'items_list'            => __( 'Help Tickets list', 'help-desk' ),
		'items_list_navigation' => __( 'Help Tickets list navigation', 'help-desk' ),
		'filter_items_list'     => __( 'Filter help tickets list', 'help-desk' ),
	);

	$args = array(
		'label'               => __( 'Help Ticket', 'help-desk' ),
		'description'         => __( 'Tickets for help requests with hierarchical status tracking', 'help-desk' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-ticket',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'help_ticket', $args );
}
add_action( 'init', 'help_register_post_types' );
