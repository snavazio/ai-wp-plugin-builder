<?php
/**
 * Register custom taxonomy for Tutorial difficulty.
 *
 * @package Tutly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the difficulty taxonomy.
 *
 * @return void
 */
function tutly_register_difficulty_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Difficulties', 'Taxonomy General Name', 'tutorials' ),
		'singular_name'              => _x( 'Difficulty', 'Taxonomy Singular Name', 'tutorials' ),
		'menu_name'                  => __( 'Difficulties', 'tutorials' ),
		'all_items'                  => __( 'All Difficulties', 'tutorials' ),
		'parent_item'                => __( 'Parent Difficulty', 'tutorials' ),
		'parent_item_colon'          => __( 'Parent Difficulty:', 'tutorials' ),
		'new_item_name'              => __( 'New Difficulty Name', 'tutorials' ),
		'add_new_item'               => __( 'Add New Difficulty', 'tutorials' ),
		'edit_item'                  => __( 'Edit Difficulty', 'tutorials' ),
		'update_item'                => __( 'Update Difficulty', 'tutorials' ),
		'view_item'                  => __( 'View Difficulty', 'tutorials' ),
		'separate_items_with_commas' => __( 'Separate difficulties with commas', 'tutorials' ),
		'add_or_remove_items'        => __( 'Add or remove difficulties', 'tutorials' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'tutorials' ),
		'popular_items'              => __( 'Popular Difficulties', 'tutorials' ),
		'search_items'               => __( 'Search Difficulties', 'tutorials' ),
		'not_found'                  => __( 'Not Found', 'tutorials' ),
		'no_terms'                   => __( 'No difficulties', 'tutorials' ),
		'items_list'                 => __( 'Difficulties list', 'tutorials' ),
		'items_list_navigation'      => __( 'Difficulties list navigation', 'tutorials' ),
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
		'rewrite'           => array( 'slug' => 'difficulty' ),
	);

	register_taxonomy( 'difficulty', array( 'tutorials' ), $args );
}
add_action( 'init', 'tutly_register_difficulty_taxonomy' );
