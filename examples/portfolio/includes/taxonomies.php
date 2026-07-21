<?php
/**
 * Register custom taxonomies.
 *
 * @package Prtf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the prtf_project_type taxonomy.
 *
 * @return void
 */
function prtf_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Project Types', 'Taxonomy General Name', 'portfolio' ),
		'singular_name'              => _x( 'Project Type', 'Taxonomy Singular Name', 'portfolio' ),
		'menu_name'                  => __( 'Project Types', 'portfolio' ),
		'all_items'                  => __( 'All Project Types', 'portfolio' ),
		'parent_item'                => __( 'Parent Project Type', 'portfolio' ),
		'parent_item_colon'          => __( 'Parent Project Type:', 'portfolio' ),
		'new_item_name'              => __( 'New Project Type Name', 'portfolio' ),
		'add_new_item'               => __( 'Add New Project Type', 'portfolio' ),
		'edit_item'                  => __( 'Edit Project Type', 'portfolio' ),
		'update_item'                => __( 'Update Project Type', 'portfolio' ),
		'view_item'                  => __( 'View Project Type', 'portfolio' ),
		'separate_items_with_commas' => __( 'Separate project types with commas', 'portfolio' ),
		'add_or_remove_items'        => __( 'Add or remove project types', 'portfolio' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'portfolio' ),
		'popular_items'              => __( 'Popular Project Types', 'portfolio' ),
		'search_items'               => __( 'Search Project Types', 'portfolio' ),
		'not_found'                  => __( 'Not Found', 'portfolio' ),
		'no_terms'                   => __( 'No project types', 'portfolio' ),
		'items_list'                 => __( 'Project Types list', 'portfolio' ),
		'items_list_navigation'      => __( 'Project Types list navigation', 'portfolio' ),
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
	);

	register_taxonomy( 'prtf_project_type', array( 'prtf_portfolio' ), $args );
}
add_action( 'init', 'prtf_register_taxonomies' );
