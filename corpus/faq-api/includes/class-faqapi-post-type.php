<?php
/**
 * Custom Post Type registration for FAQs.
 *
 * @package Faqapi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration and management of the FAQs custom post type.
 */
class Faqapi_Post_Type {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	}

	/**
	 * Register the FAQs custom post type.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		$labels = array(
			'name'                  => _x( 'FAQs', 'Post Type General Name', 'faq-api' ),
			'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'faq-api' ),
			'menu_name'             => __( 'FAQs', 'faq-api' ),
			'name_admin_bar'        => __( 'FAQ', 'faq-api' ),
			'archives'              => __( 'FAQ Archives', 'faq-api' ),
			'attributes'            => __( 'FAQ Attributes', 'faq-api' ),
			'parent_item_colon'     => __( 'Parent FAQ:', 'faq-api' ),
			'all_items'             => __( 'All FAQs', 'faq-api' ),
			'add_new_item'          => __( 'Add New FAQ', 'faq-api' ),
			'add_new'               => __( 'Add New', 'faq-api' ),
			'new_item'              => __( 'New FAQ', 'faq-api' ),
			'edit_item'             => __( 'Edit FAQ', 'faq-api' ),
			'update_item'           => __( 'Update FAQ', 'faq-api' ),
			'view_item'             => __( 'View FAQ', 'faq-api' ),
			'view_items'            => __( 'View FAQs', 'faq-api' ),
			'search_items'          => __( 'Search FAQ', 'faq-api' ),
			'not_found'             => __( 'Not found', 'faq-api' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'faq-api' ),
			'featured_image'        => __( 'Featured Image', 'faq-api' ),
			'set_featured_image'    => __( 'Set featured image', 'faq-api' ),
			'remove_featured_image' => __( 'Remove featured image', 'faq-api' ),
			'use_featured_image'    => __( 'Use as featured image', 'faq-api' ),
			'insert_into_item'      => __( 'Insert into FAQ', 'faq-api' ),
			'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'faq-api' ),
			'items_list'            => __( 'FAQs list', 'faq-api' ),
			'items_list_navigation' => __( 'FAQs list navigation', 'faq-api' ),
			'filter_items_list'     => __( 'Filter FAQs list', 'faq-api' ),
		);

		$args = array(
			'label'               => __( 'FAQ', 'faq-api' ),
			'description'         => __( 'Frequently Asked Questions', 'faq-api' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-editor-help',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rest_base'           => 'faqs',
			'show_in_rest'        => true,
		);

		register_post_type( 'faqapi_faq', $args );
	}
}
