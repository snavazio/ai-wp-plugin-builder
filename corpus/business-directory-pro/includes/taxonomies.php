<?php
/**
 * Register custom taxonomies for Business Listings.
 *
 * @package Bdp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the bdp_category taxonomy.
 *
 * @return void
 */
function bdp_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Categories', 'Taxonomy General Name', 'business-directory-pro' ),
		'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'business-directory-pro' ),
		'menu_name'                  => __( 'Categories', 'business-directory-pro' ),
		'all_items'                  => __( 'All Categories', 'business-directory-pro' ),
		'parent_item'                => __( 'Parent Category', 'business-directory-pro' ),
		'parent_item_colon'          => __( 'Parent Category:', 'business-directory-pro' ),
		'new_item_name'              => __( 'New Category Name', 'business-directory-pro' ),
		'add_new_item'               => __( 'Add New Category', 'business-directory-pro' ),
		'edit_item'                  => __( 'Edit Category', 'business-directory-pro' ),
		'update_item'                => __( 'Update Category', 'business-directory-pro' ),
		'view_item'                  => __( 'View Category', 'business-directory-pro' ),
		'separate_items_with_commas' => __( 'Separate categories with commas', 'business-directory-pro' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'business-directory-pro' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'business-directory-pro' ),
		'popular_items'              => __( 'Popular Categories', 'business-directory-pro' ),
		'search_items'               => __( 'Search Categories', 'business-directory-pro' ),
		'not_found'                  => __( 'Not Found', 'business-directory-pro' ),
		'no_terms'                   => __( 'No categories', 'business-directory-pro' ),
		'items_list'                 => __( 'Categories list', 'business-directory-pro' ),
		'items_list_navigation'      => __( 'Categories list navigation', 'business-directory-pro' ),
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
		'rewrite'           => array( 'slug' => 'category' ),
	);

	register_taxonomy( 'category', array( 'bdp_listing' ), $args );
}

/**
 * Register the bdp_region taxonomy.
 *
 * @return void
 */
function bdp_register_region_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Regions', 'Taxonomy General Name', 'business-directory-pro' ),
		'singular_name'              => _x( 'Region', 'Taxonomy Singular Name', 'business-directory-pro' ),
		'menu_name'                  => __( 'Regions', 'business-directory-pro' ),
		'all_items'                  => __( 'All Regions', 'business-directory-pro' ),
		'parent_item'                => __( 'Parent Region', 'business-directory-pro' ),
		'parent_item_colon'          => __( 'Parent Region:', 'business-directory-pro' ),
		'new_item_name'              => __( 'New Region Name', 'business-directory-pro' ),
		'add_new_item'               => __( 'Add New Region', 'business-directory-pro' ),
		'edit_item'                  => __( 'Edit Region', 'business-directory-pro' ),
		'update_item'                => __( 'Update Region', 'business-directory-pro' ),
		'view_item'                  => __( 'View Region', 'business-directory-pro' ),
		'separate_items_with_commas' => __( 'Separate regions with commas', 'business-directory-pro' ),
		'add_or_remove_items'        => __( 'Add or remove regions', 'business-directory-pro' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'business-directory-pro' ),
		'popular_items'              => __( 'Popular Regions', 'business-directory-pro' ),
		'search_items'               => __( 'Search Regions', 'business-directory-pro' ),
		'not_found'                  => __( 'Not Found', 'business-directory-pro' ),
		'no_terms'                   => __( 'No regions', 'business-directory-pro' ),
		'items_list'                 => __( 'Regions list', 'business-directory-pro' ),
		'items_list_navigation'      => __( 'Regions list navigation', 'business-directory-pro' ),
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
		'rewrite'           => array( 'slug' => 'region' ),
	);

	register_taxonomy( 'region', array( 'bdp_listing' ), $args );
}
add_action( 'init', 'bdp_register_taxonomies' );
add_action( 'init', 'bdp_register_region_taxonomy' );
