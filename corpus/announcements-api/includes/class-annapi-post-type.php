<?php
/**
 * Custom Post Type registration for Announcements.
 *
 * @package Annapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Announcements custom post type.
 */
class Annapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	}

	/**
	 * Register the Announcements custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Announcements', 'Post Type General Name', 'announcements-api' ),
			'singular_name'         => _x( 'Announcement', 'Post Type Singular Name', 'announcements-api' ),
			'menu_name'             => __( 'Announcements', 'announcements-api' ),
			'name_admin_bar'        => __( 'Announcement', 'announcements-api' ),
			'archives'              => __( 'Announcement Archives', 'announcements-api' ),
			'attributes'            => __( 'Announcement Attributes', 'announcements-api' ),
			'parent_item_colon'     => __( 'Parent Announcement:', 'announcements-api' ),
			'all_items'             => __( 'All Announcements', 'announcements-api' ),
			'add_new_item'          => __( 'Add New Announcement', 'announcements-api' ),
			'add_new'               => __( 'Add New', 'announcements-api' ),
			'new_item'              => __( 'New Announcement', 'announcements-api' ),
			'edit_item'             => __( 'Edit Announcement', 'announcements-api' ),
			'update_item'           => __( 'Update Announcement', 'announcements-api' ),
			'view_item'             => __( 'View Announcement', 'announcements-api' ),
			'view_items'            => __( 'View Announcements', 'announcements-api' ),
			'search_items'          => __( 'Search Announcement', 'announcements-api' ),
			'not_found'             => __( 'Not found', 'announcements-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'announcements-api' ),
			'featured_image'        => __( 'Featured Image', 'announcements-api' ),
			'set_featured_image'    => __( 'Set featured image', 'announcements-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'announcements-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'announcements-api' ),
			'insert_into_item'      => __( 'Insert into announcement', 'announcements-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this announcement', 'announcements-api' ),
			'items_list'            => __( 'Announcements list', 'announcements-api' ),
			'items_list_navigation' => __( 'Announcements list navigation', 'announcements-api' ),
			'filter_items_list'     => __( 'Filter announcements list', 'announcements-api' ),
		);

		$args = array(
			'label'               => __( 'Announcement', 'announcements-api' ),
			'description'         => __( 'Published announcements for public consumption', 'announcements-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-buddicons-activity',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'annapi_announcement', $args );
	}
}
