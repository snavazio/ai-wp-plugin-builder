<?php
/**
 * Register custom post type for Products.
 *
 * @package Prod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the prod_product custom post type.
 *
 * @return void
 */
function prod_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Products', 'Post Type General Name', 'products-catalog' ),
		'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'products-catalog' ),
		'menu_name'             => __( 'Products', 'products-catalog' ),
		'name_admin_bar'        => __( 'Product', 'products-catalog' ),
		'archives'              => __( 'Product Archives', 'products-catalog' ),
		'attributes'            => __( 'Product Attributes', 'products-catalog' ),
		'parent_item_colon'     => __( 'Parent Product:', 'products-catalog' ),
		'all_items'             => __( 'All Products', 'products-catalog' ),
		'add_new_item'          => __( 'Add New Product', 'products-catalog' ),
		'add_new'               => __( 'Add New', 'products-catalog' ),
		'new_item'              => __( 'New Product', 'products-catalog' ),
		'edit_item'             => __( 'Edit Product', 'products-catalog' ),
		'update_item'           => __( 'Update Product', 'products-catalog' ),
		'view_item'             => __( 'View Product', 'products-catalog' ),
		'view_items'            => __( 'View Products', 'products-catalog' ),
		'search_items'          => __( 'Search Products', 'products-catalog' ),
		'not_found'             => __( 'Not found', 'products-catalog' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'products-catalog' ),
		'featured_image'        => __( 'Featured Image', 'products-catalog' ),
		'set_featured_image'    => __( 'Set featured image', 'products-catalog' ),
		'remove_featured_image' => __( 'Remove featured image', 'products-catalog' ),
		'use_featured_image'    => __( 'Use as featured image', 'products-catalog' ),
		'insert_into_item'      => __( 'Insert into product', 'products-catalog' ),
		'uploaded_to_this_item' => __( 'Uploaded to this product', 'products-catalog' ),
		'items_list'            => __( 'Products list', 'products-catalog' ),
		'items_list_navigation' => __( 'Products list navigation', 'products-catalog' ),
		'filter_items_list'     => __( 'Filter products list', 'products-catalog' ),
	);

	$args = array(
		'label'               => __( 'Product', 'products-catalog' ),
		'description'         => __( 'Products catalog with hierarchical categories', 'products-catalog' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-products',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'product', $args );
}
add_action( 'init', 'prod_register_post_types' );
