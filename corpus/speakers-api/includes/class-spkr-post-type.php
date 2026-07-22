<?php
/**
 * Custom Post Type registration for Speakers.
 *
 * @package Spkr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the Speakers custom post type.
 */
class Spkr_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	}

	/**
	 * Register the Speakers custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Speakers', 'Post Type General Name', 'speakers-api' ),
			'singular_name'         => _x( 'Speaker', 'Post Type Singular Name', 'speakers-api' ),
			'menu_name'             => __( 'Speakers', 'speakers-api' ),
			'name_admin_bar'        => __( 'Speaker', 'speakers-api' ),
			'archives'              => __( 'Speaker Archives', 'speakers-api' ),
			'attributes'            => __( 'Speaker Attributes', 'speakers-api' ),
			'parent_item_colon'     => __( 'Parent Speaker:', 'speakers-api' ),
			'all_items'             => __( 'All Speakers', 'speakers-api' ),
			'add_new_item'          => __( 'Add New Speaker', 'speakers-api' ),
			'add_new'               => __( 'Add New', 'speakers-api' ),
			'new_item'              => __( 'New Speaker', 'speakers-api' ),
			'edit_item'             => __( 'Edit Speaker', 'speakers-api' ),
			'update_item'           => __( 'Update Speaker', 'speakers-api' ),
			'view_item'             => __( 'View Speaker', 'speakers-api' ),
			'view_items'            => __( 'View Speakers', 'speakers-api' ),
			'search_items'          => __( 'Search Speaker', 'speakers-api' ),
			'not_found'             => __( 'Not found', 'speakers-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'speakers-api' ),
			'featured_image'        => __( 'Featured Image', 'speakers-api' ),
			'set_featured_image'    => __( 'Set featured image', 'speakers-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'speakers-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'speakers-api' ),
			'insert_into_item'      => __( 'Insert into speaker', 'speakers-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this speaker', 'speakers-api' ),
			'items_list'            => __( 'Speakers list', 'speakers-api' ),
			'items_list_navigation' => __( 'Speakers list navigation', 'speakers-api' ),
			'filter_items_list'     => __( 'Filter speakers list', 'speakers-api' ),
		);

		$args = array(
			'label'               => __( 'Speaker', 'speakers-api' ),
			'description'         => __( 'Speakers with role meta', 'speakers-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-groups',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rest_base'           => 'speakers',
			'show_in_rest'        => true,
		);

		register_post_type( 'spkr_speaker', $args );
	}
}
