<?php
/**
 * Register Coupon custom post type.
 *
 * @package Cpns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the coupon custom post type.
 *
 * @return void
 */
function cpns_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Coupons', 'Post Type General Name', 'coupons' ),
		'singular_name'         => _x( 'Coupon', 'Post Type Singular Name', 'coupons' ),
		'menu_name'             => __( 'Coupons', 'coupons' ),
		'name_admin_bar'        => __( 'Coupon', 'coupons' ),
		'archives'              => __( 'Coupon Archives', 'coupons' ),
		'attributes'            => __( 'Coupon Attributes', 'coupons' ),
		'parent_item_colon'     => __( 'Parent Coupon:', 'coupons' ),
		'all_items'             => __( 'All Coupons', 'coupons' ),
		'add_new_item'          => __( 'Add New Coupon', 'coupons' ),
		'add_new'               => __( 'Add New', 'coupons' ),
		'new_item'              => __( 'New Coupon', 'coupons' ),
		'edit_item'             => __( 'Edit Coupon', 'coupons' ),
		'update_item'           => __( 'Update Coupon', 'coupons' ),
		'view_item'             => __( 'View Coupon', 'coupons' ),
		'view_items'            => __( 'View Coupons', 'coupons' ),
		'search_items'          => __( 'Search Coupons', 'coupons' ),
		'not_found'             => __( 'Not found', 'coupons' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'coupons' ),
		'featured_image'        => __( 'Featured Image', 'coupons' ),
		'set_featured_image'    => __( 'Set featured image', 'coupons' ),
		'remove_featured_image' => __( 'Remove featured image', 'coupons' ),
		'use_featured_image'    => __( 'Use as featured image', 'coupons' ),
		'insert_into_item'      => __( 'Insert into coupon', 'coupons' ),
		'uploaded_to_this_item' => __( 'Uploaded to this coupon', 'coupons' ),
		'items_list'            => __( 'Coupons list', 'coupons' ),
		'items_list_navigation' => __( 'Coupons list navigation', 'coupons' ),
		'filter_items_list'     => __( 'Filter coupons list', 'coupons' ),
	);

	$args = array(
		'label'               => __( 'Coupon', 'coupons' ),
		'description'         => __( 'Coupons with code and expiry date', 'coupons' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-tag',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'coupon', $args );
}
add_action( 'init', 'cpns_register_post_types' );
