<?php
/**
 * Register custom taxonomies for Recipes.
 *
 * @package Cook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the cook_course taxonomy.
 *
 * @return void
 */
function cook_register_course_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Courses', 'Taxonomy General Name', 'cookbook' ),
		'singular_name'              => _x( 'Course', 'Taxonomy Singular Name', 'cookbook' ),
		'menu_name'                  => __( 'Courses', 'cookbook' ),
		'all_items'                  => __( 'All Courses', 'cookbook' ),
		'parent_item'                => __( 'Parent Course', 'cookbook' ),
		'parent_item_colon'          => __( 'Parent Course:', 'cookbook' ),
		'new_item_name'              => __( 'New Course Name', 'cookbook' ),
		'add_new_item'               => __( 'Add New Course', 'cookbook' ),
		'edit_item'                  => __( 'Edit Course', 'cookbook' ),
		'update_item'                => __( 'Update Course', 'cookbook' ),
		'view_item'                  => __( 'View Course', 'cookbook' ),
		'separate_items_with_commas' => __( 'Separate courses with commas', 'cookbook' ),
		'add_or_remove_items'        => __( 'Add or remove courses', 'cookbook' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'cookbook' ),
		'popular_items'              => __( 'Popular Courses', 'cookbook' ),
		'search_items'               => __( 'Search Courses', 'cookbook' ),
		'not_found'                  => __( 'Not Found', 'cookbook' ),
		'no_terms'                   => __( 'No courses', 'cookbook' ),
		'items_list'                 => __( 'Courses list', 'cookbook' ),
		'items_list_navigation'      => __( 'Courses list navigation', 'cookbook' ),
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
		'rewrite'           => array( 'slug' => 'course' ),
	);

	register_taxonomy( 'cook_course', array( 'recipe' ), $args );
}
add_action( 'init', 'cook_register_course_taxonomy' );

/**
 * Register the cook_cuisine taxonomy.
 *
 * @return void
 */
function cook_register_cuisine_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Cuisines', 'Taxonomy General Name', 'cookbook' ),
		'singular_name'              => _x( 'Cuisine', 'Taxonomy Singular Name', 'cookbook' ),
		'menu_name'                  => __( 'Cuisines', 'cookbook' ),
		'all_items'                  => __( 'All Cuisines', 'cookbook' ),
		'parent_item'                => __( 'Parent Cuisine', 'cookbook' ),
		'parent_item_colon'          => __( 'Parent Cuisine:', 'cookbook' ),
		'new_item_name'              => __( 'New Cuisine Name', 'cookbook' ),
		'add_new_item'               => __( 'Add New Cuisine', 'cookbook' ),
		'edit_item'                  => __( 'Edit Cuisine', 'cookbook' ),
		'update_item'                => __( 'Update Cuisine', 'cookbook' ),
		'view_item'                  => __( 'View Cuisine', 'cookbook' ),
		'separate_items_with_commas' => __( 'Separate cuisines with commas', 'cookbook' ),
		'add_or_remove_items'        => __( 'Add or remove cuisines', 'cookbook' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'cookbook' ),
		'popular_items'              => __( 'Popular Cuisines', 'cookbook' ),
		'search_items'               => __( 'Search Cuisines', 'cookbook' ),
		'not_found'                  => __( 'Not Found', 'cookbook' ),
		'no_terms'                   => __( 'No cuisines', 'cookbook' ),
		'items_list'                 => __( 'Cuisines list', 'cookbook' ),
		'items_list_navigation'      => __( 'Cuisines list navigation', 'cookbook' ),
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
		'rewrite'           => array( 'slug' => 'cuisine' ),
	);

	register_taxonomy( 'cook_cuisine', array( 'recipe' ), $args );
}
add_action( 'init', 'cook_register_cuisine_taxonomy' );
