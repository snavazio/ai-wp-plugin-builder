<?php
/**
 * Register Product custom post type.
 *
 * @package Strp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the product custom post type.
 *
 * @return void
 */
function strp_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Products', 'Post Type General Name', 'store-products' ),
		'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'store-products' ),
		'menu_name'             => __( 'Products', 'store-products' ),
		'name_admin_bar'        => __( 'Product', 'store-products' ),
		'archives'              => __( 'Product Archives', 'store-products' ),
		'attributes'            => __( 'Product Attributes', 'store-products' ),
		'parent_item_colon'     => __( 'Parent Product:', 'store-products' ),
		'all_items'             => __( 'All Products', 'store-products' ),
		'add_new_item'          => __( 'Add New Product', 'store-products' ),
		'add_new'               => __( 'Add New', 'store-products' ),
		'new_item'              => __( 'New Product', 'store-products' ),
		'edit_item'             => __( 'Edit Product', 'store-products' ),
		'update_item'           => __( 'Update Product', 'store-products' ),
		'view_item'             => __( 'View Product', 'store-products' ),
		'view_items'            => __( 'View Products', 'store-products' ),
		'search_items'          => __( 'Search Products', 'store-products' ),
		'not_found'             => __( 'Not found', 'store-products' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'store-products' ),
		'featured_image'        => __( 'Featured Image', 'store-products' ),
		'set_featured_image'    => __( 'Set featured image', 'store-products' ),
		'remove_featured_image' => __( 'Remove featured image', 'store-products' ),
		'use_featured_image'    => __( 'Use as featured image', 'store-products' ),
		'insert_into_item'      => __( 'Insert into product', 'store-products' ),
		'uploaded_to_this_item' => __( 'Uploaded to this product', 'store-products' ),
		'items_list'            => __( 'Products list', 'store-products' ),
		'items_list_navigation' => __( 'Products list navigation', 'store-products' ),
		'filter_items_list'     => __( 'Filter products list', 'store-products' ),
	);

	$args = array(
		'label'               => __( 'Product', 'store-products' ),
		'description'         => __( 'Products with price and SKU', 'store-products' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-cart',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'product', $args );
}
add_action( 'init', 'strp_register_post_types' );
