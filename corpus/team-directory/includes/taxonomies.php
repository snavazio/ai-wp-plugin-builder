<?php
/**
 * Register custom taxonomy for Departments.
 *
 * @package Tdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the tdir_department taxonomy.
 *
 * @return void
 */
function tdir_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Departments', 'Taxonomy General Name', 'team-directory' ),
		'singular_name'              => _x( 'Department', 'Taxonomy Singular Name', 'team-directory' ),
		'menu_name'                  => __( 'Departments', 'team-directory' ),
		'all_items'                  => __( 'All Departments', 'team-directory' ),
		'parent_item'                => __( 'Parent Department', 'team-directory' ),
		'parent_item_colon'          => __( 'Parent Department:', 'team-directory' ),
		'new_item_name'              => __( 'New Department Name', 'team-directory' ),
		'add_new_item'               => __( 'Add New Department', 'team-directory' ),
		'edit_item'                  => __( 'Edit Department', 'team-directory' ),
		'update_item'                => __( 'Update Department', 'team-directory' ),
		'view_item'                  => __( 'View Department', 'team-directory' ),
		'separate_items_with_commas' => __( 'Separate departments with commas', 'team-directory' ),
		'add_or_remove_items'        => __( 'Add or remove departments', 'team-directory' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'team-directory' ),
		'popular_items'              => __( 'Popular Departments', 'team-directory' ),
		'search_items'               => __( 'Search Departments', 'team-directory' ),
		'not_found'                  => __( 'Not Found', 'team-directory' ),
		'no_terms'                   => __( 'No departments', 'team-directory' ),
		'items_list'                 => __( 'Departments list', 'team-directory' ),
		'items_list_navigation'      => __( 'Departments list navigation', 'team-directory' ),
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
		'rewrite'           => array( 'slug' => 'department' ),
	);

	register_taxonomy( 'tdir_department', array( 'tdir_team_member' ), $args );
}
add_action( 'init', 'tdir_register_taxonomies' );
