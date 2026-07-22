<?php
/**
 * Register custom taxonomies for Comics.
 *
 * @package Comics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the comics_publisher taxonomy.
 *
 * @return void
 */
function comics_register_publisher_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Publishers', 'Taxonomy General Name', 'comics' ),
		'singular_name'              => _x( 'Publisher', 'Taxonomy Singular Name', 'comics' ),
		'menu_name'                  => __( 'Publishers', 'comics' ),
		'all_items'                  => __( 'All Publishers', 'comics' ),
		'parent_item'                => __( 'Parent Publisher', 'comics' ),
		'parent_item_colon'          => __( 'Parent Publisher:', 'comics' ),
		'new_item_name'              => __( 'New Publisher Name', 'comics' ),
		'add_new_item'               => __( 'Add New Publisher', 'comics' ),
		'edit_item'                  => __( 'Edit Publisher', 'comics' ),
		'update_item'                => __( 'Update Publisher', 'comics' ),
		'view_item'                  => __( 'View Publisher', 'comics' ),
		'separate_items_with_commas' => __( 'Separate publishers with commas', 'comics' ),
		'add_or_remove_items'        => __( 'Add or remove publishers', 'comics' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'comics' ),
		'popular_items'              => __( 'Popular Publishers', 'comics' ),
		'search_items'               => __( 'Search Publishers', 'comics' ),
		'not_found'                  => __( 'Not Found', 'comics' ),
		'no_terms'                   => __( 'No publishers', 'comics' ),
		'items_list'                 => __( 'Publishers list', 'comics' ),
		'items_list_navigation'      => __( 'Publishers list navigation', 'comics' ),
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
		'rewrite'           => array( 'slug' => 'publisher' ),
	);

	register_taxonomy( 'comics_publisher', array( 'comic' ), $args );
}
add_action( 'init', 'comics_register_publisher_taxonomy' );

/**
 * Register the comics_series taxonomy.
 *
 * @return void
 */
function comics_register_series_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Series', 'Taxonomy General Name', 'comics' ),
		'singular_name'              => _x( 'Series', 'Taxonomy Singular Name', 'comics' ),
		'menu_name'                  => __( 'Series', 'comics' ),
		'all_items'                  => __( 'All Series', 'comics' ),
		'parent_item'                => __( 'Parent Series', 'comics' ),
		'parent_item_colon'          => __( 'Parent Series:', 'comics' ),
		'new_item_name'              => __( 'New Series Name', 'comics' ),
		'add_new_item'               => __( 'Add New Series', 'comics' ),
		'edit_item'                  => __( 'Edit Series', 'comics' ),
		'update_item'                => __( 'Update Series', 'comics' ),
		'view_item'                  => __( 'View Series', 'comics' ),
		'separate_items_with_commas' => __( 'Separate series with commas', 'comics' ),
		'add_or_remove_items'        => __( 'Add or remove series', 'comics' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'comics' ),
		'popular_items'              => __( 'Popular Series', 'comics' ),
		'search_items'               => __( 'Search Series', 'comics' ),
		'not_found'                  => __( 'Not Found', 'comics' ),
		'no_terms'                   => __( 'No series', 'comics' ),
		'items_list'                 => __( 'Series list', 'comics' ),
		'items_list_navigation'      => __( 'Series list navigation', 'comics' ),
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
		'rewrite'           => array( 'slug' => 'series' ),
	);

	register_taxonomy( 'comics_series', array( 'comic' ), $args );
}
add_action( 'init', 'comics_register_series_taxonomy' );
