<?php
/**
 * Register custom taxonomy for Beer Styles.
 *
 * @package Beers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the style taxonomy.
 *
 * @return void
 */
function beers_register_style_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Styles', 'Taxonomy General Name', 'beer-list' ),
		'singular_name'              => _x( 'Style', 'Taxonomy Singular Name', 'beer-list' ),
		'menu_name'                  => __( 'Styles', 'beer-list' ),
		'all_items'                  => __( 'All Styles', 'beer-list' ),
		'parent_item'                => __( 'Parent Style', 'beer-list' ),
		'parent_item_colon'          => __( 'Parent Style:', 'beer-list' ),
		'new_item_name'              => __( 'New Style Name', 'beer-list' ),
		'add_new_item'               => __( 'Add New Style', 'beer-list' ),
		'edit_item'                  => __( 'Edit Style', 'beer-list' ),
		'update_item'                => __( 'Update Style', 'beer-list' ),
		'view_item'                  => __( 'View Style', 'beer-list' ),
		'separate_items_with_commas' => __( 'Separate styles with commas', 'beer-list' ),
		'add_or_remove_items'        => __( 'Add or remove styles', 'beer-list' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'beer-list' ),
		'popular_items'              => __( 'Popular Styles', 'beer-list' ),
		'search_items'               => __( 'Search Styles', 'beer-list' ),
		'not_found'                  => __( 'Not Found', 'beer-list' ),
		'no_terms'                   => __( 'No styles', 'beer-list' ),
		'items_list'                 => __( 'Styles list', 'beer-list' ),
		'items_list_navigation'      => __( 'Styles list navigation', 'beer-list' ),
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
		'rewrite'           => array( 'slug' => 'beer-style' ),
	);

	register_taxonomy( 'style', array( 'beer' ), $args );
}
add_action( 'init', 'beers_register_style_taxonomy' );
