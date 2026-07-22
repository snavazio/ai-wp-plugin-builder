<?php
/**
 * Register custom taxonomy for Topics.
 *
 * @package Kbpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the kbpl_topic taxonomy.
 *
 * @return void
 */
function kbpl_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Topics', 'Taxonomy General Name', 'knowledge-base' ),
		'singular_name'              => _x( 'Topic', 'Taxonomy Singular Name', 'knowledge-base' ),
		'menu_name'                  => __( 'Topics', 'knowledge-base' ),
		'all_items'                  => __( 'All Topics', 'knowledge-base' ),
		'parent_item'                => __( 'Parent Topic', 'knowledge-base' ),
		'parent_item_colon'          => __( 'Parent Topic:', 'knowledge-base' ),
		'new_item_name'              => __( 'New Topic Name', 'knowledge-base' ),
		'add_new_item'               => __( 'Add New Topic', 'knowledge-base' ),
		'edit_item'                  => __( 'Edit Topic', 'knowledge-base' ),
		'update_item'                => __( 'Update Topic', 'knowledge-base' ),
		'view_item'                  => __( 'View Topic', 'knowledge-base' ),
		'separate_items_with_commas' => __( 'Separate topics with commas', 'knowledge-base' ),
		'add_or_remove_items'        => __( 'Add or remove topics', 'knowledge-base' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'knowledge-base' ),
		'popular_items'              => __( 'Popular Topics', 'knowledge-base' ),
		'search_items'               => __( 'Search Topics', 'knowledge-base' ),
		'not_found'                  => __( 'Not Found', 'knowledge-base' ),
		'no_terms'                   => __( 'No topics', 'knowledge-base' ),
		'items_list'                 => __( 'Topics list', 'knowledge-base' ),
		'items_list_navigation'      => __( 'Topics list navigation', 'knowledge-base' ),
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
		'rewrite'           => array( 'slug' => 'topic' ),
	);

	register_taxonomy( 'kbpl_topic', array( 'kbpl_article' ), $args );
}
add_action( 'init', 'kbpl_register_taxonomies' );
