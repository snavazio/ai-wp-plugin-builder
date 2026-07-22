<?php
/**
 * Register custom taxonomy for Plant Families.
 *
 * @package Pgui
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the pgui_family taxonomy.
 *
 * @return void
 */
function pgui_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Families', 'Taxonomy General Name', 'plant-guide' ),
		'singular_name'              => _x( 'Family', 'Taxonomy Singular Name', 'plant-guide' ),
		'menu_name'                  => __( 'Families', 'plant-guide' ),
		'all_items'                  => __( 'All Families', 'plant-guide' ),
		'parent_item'                => __( 'Parent Family', 'plant-guide' ),
		'parent_item_colon'          => __( 'Parent Family:', 'plant-guide' ),
		'new_item_name'              => __( 'New Family Name', 'plant-guide' ),
		'add_new_item'               => __( 'Add New Family', 'plant-guide' ),
		'edit_item'                  => __( 'Edit Family', 'plant-guide' ),
		'update_item'                => __( 'Update Family', 'plant-guide' ),
		'view_item'                  => __( 'View Family', 'plant-guide' ),
		'separate_items_with_commas' => __( 'Separate families with commas', 'plant-guide' ),
		'add_or_remove_items'        => __( 'Add or remove families', 'plant-guide' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'plant-guide' ),
		'popular_items'              => __( 'Popular Families', 'plant-guide' ),
		'search_items'               => __( 'Search Families', 'plant-guide' ),
		'not_found'                  => __( 'Not Found', 'plant-guide' ),
		'no_terms'                   => __( 'No families', 'plant-guide' ),
		'items_list'                 => __( 'Families list', 'plant-guide' ),
		'items_list_navigation'      => __( 'Families list navigation', 'plant-guide' ),
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
		'rewrite'           => array( 'slug' => 'family' ),
	);

	register_taxonomy( 'pgui_family', array( 'pgui_plant' ), $args );
}
add_action( 'init', 'pgui_register_taxonomies' );
