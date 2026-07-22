<?php
/**
 * Register custom taxonomy for Job Categories.
 *
 * @package Jbrd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the jbrd_job_category taxonomy.
 *
 * @return void
 */
function jbrd_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Job Categories', 'Taxonomy General Name', 'job-board' ),
		'singular_name'              => _x( 'Job Category', 'Taxonomy Singular Name', 'job-board' ),
		'menu_name'                  => __( 'Job Categories', 'job-board' ),
		'all_items'                  => __( 'All Job Categories', 'job-board' ),
		'parent_item'                => __( 'Parent Job Category', 'job-board' ),
		'parent_item_colon'          => __( 'Parent Job Category:', 'job-board' ),
		'new_item_name'              => __( 'New Job Category Name', 'job-board' ),
		'add_new_item'               => __( 'Add New Job Category', 'job-board' ),
		'edit_item'                  => __( 'Edit Job Category', 'job-board' ),
		'update_item'                => __( 'Update Job Category', 'job-board' ),
		'view_item'                  => __( 'View Job Category', 'job-board' ),
		'separate_items_with_commas' => __( 'Separate job categories with commas', 'job-board' ),
		'add_or_remove_items'        => __( 'Add or remove job categories', 'job-board' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'job-board' ),
		'popular_items'              => __( 'Popular Job Categories', 'job-board' ),
		'search_items'               => __( 'Search Job Categories', 'job-board' ),
		'not_found'                  => __( 'Not Found', 'job-board' ),
		'no_terms'                   => __( 'No job categories', 'job-board' ),
		'items_list'                 => __( 'Job Categories list', 'job-board' ),
		'items_list_navigation'      => __( 'Job Categories list navigation', 'job-board' ),
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
		'rewrite'           => array( 'slug' => 'job-category' ),
	);

	register_taxonomy( 'jbrd_job_category', array( 'jbrd_job' ), $args );
}
add_action( 'init', 'jbrd_register_taxonomies' );
