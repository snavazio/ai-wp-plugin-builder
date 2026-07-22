<?php
/**
 * Register custom taxonomy for Contact Status.
 *
 * @package Scrm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the scrm_status taxonomy.
 *
 * @return void
 */
function scrm_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Statuses', 'Taxonomy General Name', 'simple-crm' ),
		'singular_name'              => _x( 'Status', 'Taxonomy Singular Name', 'simple-crm' ),
		'menu_name'                  => __( 'Statuses', 'simple-crm' ),
		'all_items'                  => __( 'All Statuses', 'simple-crm' ),
		'parent_item'                => __( 'Parent Status', 'simple-crm' ),
		'parent_item_colon'          => __( 'Parent Status:', 'simple-crm' ),
		'new_item_name'              => __( 'New Status Name', 'simple-crm' ),
		'add_new_item'               => __( 'Add New Status', 'simple-crm' ),
		'edit_item'                  => __( 'Edit Status', 'simple-crm' ),
		'update_item'                => __( 'Update Status', 'simple-crm' ),
		'view_item'                  => __( 'View Status', 'simple-crm' ),
		'separate_items_with_commas' => __( 'Separate statuses with commas', 'simple-crm' ),
		'add_or_remove_items'        => __( 'Add or remove statuses', 'simple-crm' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'simple-crm' ),
		'popular_items'              => __( 'Popular Statuses', 'simple-crm' ),
		'search_items'               => __( 'Search Statuses', 'simple-crm' ),
		'not_found'                  => __( 'Not Found', 'simple-crm' ),
		'no_terms'                   => __( 'No statuses', 'simple-crm' ),
		'items_list'                 => __( 'Statuses list', 'simple-crm' ),
		'items_list_navigation'      => __( 'Statuses list navigation', 'simple-crm' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'contact-status' ),
	);

	register_taxonomy( 'scrm_status', array( 'scrm_contact' ), $args );
}
add_action( 'init', 'scrm_register_taxonomies' );
