<?php
/**
 * Register custom taxonomy for Product Categories.
 *
 * @package Prod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the prod_product_category taxonomy.
 *
 * @return void
 */
function prod_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Product Categories', 'Taxonomy General Name', 'products-catalog' ),
		'singular_name'              => _x( 'Product Category', 'Taxonomy Singular Name', 'products-catalog' ),
		'menu_name'                  => __( 'Product Categories', 'products-catalog' ),
		'all_items'                  => __( 'All Product Categories', 'products-catalog' ),
		'parent_item'                => __( 'Parent Product Category', 'products-catalog' ),
		'parent_item_colon'          => __( 'Parent Product Category:', 'products-catalog' ),
		'new_item_name'              => __( 'New Product Category Name', 'products-catalog' ),
		'add_new_item'               => __( 'Add New Product Category', 'products-catalog' ),
		'edit_item'                  => __( 'Edit Product Category', 'products-catalog' ),
		'update_item'                => __( 'Update Product Category', 'products-catalog' ),
		'view_item'                  => __( 'View Product Category', 'products-catalog' ),
		'separate_items_with_commas' => __( 'Separate product categories with commas', 'products-catalog' ),
		'add_or_remove_items'        => __( 'Add or remove product categories', 'products-catalog' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'products-catalog' ),
		'popular_items'              => __( 'Popular Product Categories', 'products-catalog' ),
		'search_items'               => __( 'Search Product Categories', 'products-catalog' ),
		'not_found'                  => __( 'Not Found', 'products-catalog' ),
		'no_terms'                   => __( 'No product categories', 'products-catalog' ),
		'items_list'                 => __( 'Product Categories list', 'products-catalog' ),
		'items_list_navigation'      => __( 'Product Categories list navigation', 'products-catalog' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'product-category' ),
	);

	register_taxonomy( 'product_category', array( 'product' ), $args );
}
add_action( 'init', 'prod_register_taxonomies' );
