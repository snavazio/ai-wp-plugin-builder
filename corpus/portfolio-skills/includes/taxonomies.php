<?php
/**
 * Register custom taxonomy for Skills.
 *
 * @package Pskl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the pskl_skill taxonomy.
 *
 * @return void
 */
function pskl_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Skills', 'Taxonomy General Name', 'portfolio-skills' ),
		'singular_name'              => _x( 'Skill', 'Taxonomy Singular Name', 'portfolio-skills' ),
		'menu_name'                  => __( 'Skills', 'portfolio-skills' ),
		'all_items'                  => __( 'All Skills', 'portfolio-skills' ),
		'parent_item'                => __( 'Parent Skill', 'portfolio-skills' ),
		'parent_item_colon'          => __( 'Parent Skill:', 'portfolio-skills' ),
		'new_item_name'              => __( 'New Skill Name', 'portfolio-skills' ),
		'add_new_item'               => __( 'Add New Skill', 'portfolio-skills' ),
		'edit_item'                  => __( 'Edit Skill', 'portfolio-skills' ),
		'update_item'                => __( 'Update Skill', 'portfolio-skills' ),
		'view_item'                  => __( 'View Skill', 'portfolio-skills' ),
		'separate_items_with_commas' => __( 'Separate skills with commas', 'portfolio-skills' ),
		'add_or_remove_items'        => __( 'Add or remove skills', 'portfolio-skills' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'portfolio-skills' ),
		'popular_items'              => __( 'Popular Skills', 'portfolio-skills' ),
		'search_items'               => __( 'Search Skills', 'portfolio-skills' ),
		'not_found'                  => __( 'Not Found', 'portfolio-skills' ),
		'no_terms'                   => __( 'No skills', 'portfolio-skills' ),
		'items_list'                 => __( 'Skills list', 'portfolio-skills' ),
		'items_list_navigation'      => __( 'Skills list navigation', 'portfolio-skills' ),
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
		'rewrite'           => array( 'slug' => 'skill' ),
	);

	register_taxonomy( 'pskl_skill', array( 'pskl_work' ), $args );
}
add_action( 'init', 'pskl_register_taxonomies' );
