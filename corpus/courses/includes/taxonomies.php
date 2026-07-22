<?php
/**
 * Register custom taxonomies for Courses.
 *
 * @package Crs1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the crs1_subject taxonomy.
 *
 * @return void
 */
function crs1_register_subject_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Subjects', 'Taxonomy General Name', 'courses' ),
		'singular_name'              => _x( 'Subject', 'Taxonomy Singular Name', 'courses' ),
		'menu_name'                  => __( 'Subjects', 'courses' ),
		'all_items'                  => __( 'All Subjects', 'courses' ),
		'parent_item'                => __( 'Parent Subject', 'courses' ),
		'parent_item_colon'          => __( 'Parent Subject:', 'courses' ),
		'new_item_name'              => __( 'New Subject Name', 'courses' ),
		'add_new_item'               => __( 'Add New Subject', 'courses' ),
		'edit_item'                  => __( 'Edit Subject', 'courses' ),
		'update_item'                => __( 'Update Subject', 'courses' ),
		'view_item'                  => __( 'View Subject', 'courses' ),
		'separate_items_with_commas' => __( 'Separate subjects with commas', 'courses' ),
		'add_or_remove_items'        => __( 'Add or remove subjects', 'courses' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'courses' ),
		'popular_items'              => __( 'Popular Subjects', 'courses' ),
		'search_items'               => __( 'Search Subjects', 'courses' ),
		'not_found'                  => __( 'Not Found', 'courses' ),
		'no_terms'                   => __( 'No subjects', 'courses' ),
		'items_list'                 => __( 'Subjects list', 'courses' ),
		'items_list_navigation'      => __( 'Subjects list navigation', 'courses' ),
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
		'rewrite'           => array( 'slug' => 'subject' ),
	);

	register_taxonomy( 'crs1_subject', array( 'crs1_course' ), $args );
}

/**
 * Register the crs1_level taxonomy.
 *
 * @return void
 */
function crs1_register_level_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Levels', 'Taxonomy General Name', 'courses' ),
		'singular_name'              => _x( 'Level', 'Taxonomy Singular Name', 'courses' ),
		'menu_name'                  => __( 'Levels', 'courses' ),
		'all_items'                  => __( 'All Levels', 'courses' ),
		'parent_item'                => __( 'Parent Level', 'courses' ),
		'parent_item_colon'          => __( 'Parent Level:', 'courses' ),
		'new_item_name'              => __( 'New Level Name', 'courses' ),
		'add_new_item'               => __( 'Add New Level', 'courses' ),
		'edit_item'                  => __( 'Edit Level', 'courses' ),
		'update_item'                => __( 'Update Level', 'courses' ),
		'view_item'                  => __( 'View Level', 'courses' ),
		'separate_items_with_commas' => __( 'Separate levels with commas', 'courses' ),
		'add_or_remove_items'        => __( 'Add or remove levels', 'courses' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'courses' ),
		'popular_items'              => __( 'Popular Levels', 'courses' ),
		'search_items'               => __( 'Search Levels', 'courses' ),
		'not_found'                  => __( 'Not Found', 'courses' ),
		'no_terms'                   => __( 'No levels', 'courses' ),
		'items_list'                 => __( 'Levels list', 'courses' ),
		'items_list_navigation'      => __( 'Levels list navigation', 'courses' ),
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
		'rewrite'           => array( 'slug' => 'level' ),
	);

	register_taxonomy( 'crs1_level', array( 'crs1_course' ), $args );
}

add_action( 'init', 'crs1_register_subject_taxonomy' );
add_action( 'init', 'crs1_register_level_taxonomy' );
