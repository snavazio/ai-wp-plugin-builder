<?php
/**
 * Register custom taxonomies for Business Categories and Regions.
 *
 * @package Bdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the bdir_category taxonomy.
 *
 * @return void
 */
function bdir_register_category_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Business Categories', 'Taxonomy General Name', 'business-directory' ),
		'singular_name'              => _x( 'Business Category', 'Taxonomy Singular Name', 'business-directory' ),
		'menu_name'                  => __( 'Business Categories', 'business-directory' ),
		'all_items'                  => __( 'All Business Categories', 'business-directory' ),
		'parent_item'                => __( 'Parent Business Category', 'business-directory' ),
		'parent_item_colon'          => __( 'Parent Business Category:', 'business-directory' ),
		'new_item_name'              => __( 'New Business Category Name', 'business-directory' ),
		'add_new_item'               => __( 'Add New Business Category', 'business-directory' ),
		'edit_item'                  => __( 'Edit Business Category', 'business-directory' ),
		'update_item'                => __( 'Update Business Category', 'business-directory' ),
		'view_item'                  => __( 'View Business Category', 'business-directory' ),
		'separate_items_with_commas' => __( 'Separate business categories with commas', 'business-directory' ),
		'add_or_remove_items'        => __( 'Add or remove business categories', 'business-directory' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'business-directory' ),
		'popular_items'              => __( 'Popular Business Categories', 'business-directory' ),
		'search_items'               => __( 'Search Business Categories', 'business-directory' ),
		'not_found'                  => __( 'Not Found', 'business-directory' ),
		'no_terms'                   => __( 'No business categories', 'business-directory' ),
		'items_list'                 => __( 'Business Categories list', 'business-directory' ),
		'items_list_navigation'      => __( 'Business Categories list navigation', 'business-directory' ),
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
		'rewrite'           => array( 'slug' => 'business-category' ),
	);

	register_taxonomy( 'bdir_category', array( 'business' ), $args );
}

/**
 * Register the bdir_region taxonomy.
 *
 * @return void
 */
function bdir_register_region_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Business Regions', 'Taxonomy General Name', 'business-directory' ),
		'singular_name'              => _x( 'Business Region', 'Taxonomy Singular Name', 'business-directory' ),
		'menu_name'                  => __( 'Business Regions', 'business-directory' ),
		'all_items'                  => __( 'All Business Regions', 'business-directory' ),
		'parent_item'                => __( 'Parent Business Region', 'business-directory' ),
		'parent_item_colon'          => __( 'Parent Business Region:', 'business-directory' ),
		'new_item_name'              => __( 'New Business Region Name', 'business-directory' ),
		'add_new_item'               => __( 'Add New Business Region', 'business-directory' ),
		'edit_item'                  => __( 'Edit Business Region', 'business-directory' ),
		'update_item'                => __( 'Update Business Region', 'business-directory' ),
		'view_item'                  => __( 'View Business Region', 'business-directory' ),
		'separate_items_with_commas' => __( 'Separate business regions with commas', 'business-directory' ),
		'add_or_remove_items'        => __( 'Add or remove business regions', 'business-directory' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'business-directory' ),
		'popular_items'              => __( 'Popular Business Regions', 'business-directory' ),
		'search_items'               => __( 'Search Business Regions', 'business-directory' ),
		'not_found'                  => __( 'Not Found', 'business-directory' ),
		'no_terms'                   => __( 'No business regions', 'business-directory' ),
		'items_list'                 => __( 'Business Regions list', 'business-directory' ),
		'items_list_navigation'      => __( 'Business Regions list navigation', 'business-directory' ),
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
		'rewrite'           => array( 'slug' => 'business-region' ),
	);

	register_taxonomy( 'bdir_region', array( 'business' ), $args );
}

add_action( 'init', 'bdir_register_category_taxonomy' );
add_action( 'init', 'bdir_register_region_taxonomy' );
