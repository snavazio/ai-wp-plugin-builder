<?php
/**
 * Register custom taxonomy for Podcast Seasons.
 *
 * @package Podc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the season taxonomy.
 *
 * @return void
 */
function podc_register_season_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Seasons', 'Taxonomy General Name', 'podcast-episodes' ),
		'singular_name'              => _x( 'Season', 'Taxonomy Singular Name', 'podcast-episodes' ),
		'menu_name'                  => __( 'Seasons', 'podcast-episodes' ),
		'all_items'                  => __( 'All Seasons', 'podcast-episodes' ),
		'parent_item'                => __( 'Parent Season', 'podcast-episodes' ),
		'parent_item_colon'          => __( 'Parent Season:', 'podcast-episodes' ),
		'new_item_name'              => __( 'New Season Name', 'podcast-episodes' ),
		'add_new_item'               => __( 'Add New Season', 'podcast-episodes' ),
		'edit_item'                  => __( 'Edit Season', 'podcast-episodes' ),
		'update_item'                => __( 'Update Season', 'podcast-episodes' ),
		'view_item'                  => __( 'View Season', 'podcast-episodes' ),
		'separate_items_with_commas' => __( 'Separate seasons with commas', 'podcast-episodes' ),
		'add_or_remove_items'        => __( 'Add or remove seasons', 'podcast-episodes' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'podcast-episodes' ),
		'popular_items'              => __( 'Popular Seasons', 'podcast-episodes' ),
		'search_items'               => __( 'Search Seasons', 'podcast-episodes' ),
		'not_found'                  => __( 'Not Found', 'podcast-episodes' ),
		'no_terms'                   => __( 'No seasons', 'podcast-episodes' ),
		'items_list'                 => __( 'Seasons list', 'podcast-episodes' ),
		'items_list_navigation'      => __( 'Seasons list navigation', 'podcast-episodes' ),
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
		'rewrite'           => array( 'slug' => 'season' ),
	);

	register_taxonomy( 'season', array( 'episode' ), $args );
}
add_action( 'init', 'podc_register_season_taxonomy' );
