<?php
/**
 * Register custom taxonomies for Wine Regions and Varietals.
 *
 * @package Winec
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the region taxonomy.
 *
 * @return void
 */
function winec_register_region_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Regions', 'Taxonomy General Name', 'wine-catalog' ),
		'singular_name'              => _x( 'Region', 'Taxonomy Singular Name', 'wine-catalog' ),
		'menu_name'                  => __( 'Regions', 'wine-catalog' ),
		'all_items'                  => __( 'All Regions', 'wine-catalog' ),
		'parent_item'                => __( 'Parent Region', 'wine-catalog' ),
		'parent_item_colon'          => __( 'Parent Region:', 'wine-catalog' ),
		'new_item_name'              => __( 'New Region Name', 'wine-catalog' ),
		'add_new_item'               => __( 'Add New Region', 'wine-catalog' ),
		'edit_item'                  => __( 'Edit Region', 'wine-catalog' ),
		'update_item'                => __( 'Update Region', 'wine-catalog' ),
		'view_item'                  => __( 'View Region', 'wine-catalog' ),
		'separate_items_with_commas' => __( 'Separate regions with commas', 'wine-catalog' ),
		'add_or_remove_items'        => __( 'Add or remove regions', 'wine-catalog' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'wine-catalog' ),
		'popular_items'              => __( 'Popular Regions', 'wine-catalog' ),
		'search_items'               => __( 'Search Regions', 'wine-catalog' ),
		'not_found'                  => __( 'Not Found', 'wine-catalog' ),
		'no_terms'                   => __( 'No regions', 'wine-catalog' ),
		'items_list'                 => __( 'Regions list', 'wine-catalog' ),
		'items_list_navigation'      => __( 'Regions list navigation', 'wine-catalog' ),
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
		'rewrite'           => array( 'slug' => 'wine-region' ),
	);

	register_taxonomy( 'region', array( 'wine' ), $args );
}
add_action( 'init', 'winec_register_region_taxonomy' );

/**
 * Register the varietal taxonomy.
 *
 * @return void
 */
function winec_register_varietal_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Varietals', 'Taxonomy General Name', 'wine-catalog' ),
		'singular_name'              => _x( 'Varietal', 'Taxonomy Singular Name', 'wine-catalog' ),
		'menu_name'                  => __( 'Varietals', 'wine-catalog' ),
		'all_items'                  => __( 'All Varietals', 'wine-catalog' ),
		'parent_item'                => __( 'Parent Varietal', 'wine-catalog' ),
		'parent_item_colon'          => __( 'Parent Varietal:', 'wine-catalog' ),
		'new_item_name'              => __( 'New Varietal Name', 'wine-catalog' ),
		'add_new_item'               => __( 'Add New Varietal', 'wine-catalog' ),
		'edit_item'                  => __( 'Edit Varietal', 'wine-catalog' ),
		'update_item'                => __( 'Update Varietal', 'wine-catalog' ),
		'view_item'                  => __( 'View Varietal', 'wine-catalog' ),
		'separate_items_with_commas' => __( 'Separate varietals with commas', 'wine-catalog' ),
		'add_or_remove_items'        => __( 'Add or remove varietals', 'wine-catalog' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'wine-catalog' ),
		'popular_items'              => __( 'Popular Varietals', 'wine-catalog' ),
		'search_items'               => __( 'Search Varietals', 'wine-catalog' ),
		'not_found'                  => __( 'Not Found', 'wine-catalog' ),
		'no_terms'                   => __( 'No varietals', 'wine-catalog' ),
		'items_list'                 => __( 'Varietals list', 'wine-catalog' ),
		'items_list_navigation'      => __( 'Varietals list navigation', 'wine-catalog' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'wine-varietal' ),
	);

	register_taxonomy( 'varietal', array( 'wine' ), $args );
}
add_action( 'init', 'winec_register_varietal_taxonomy' );
