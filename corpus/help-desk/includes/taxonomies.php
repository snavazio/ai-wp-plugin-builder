<?php
/**
 * Register custom taxonomy for Help Status.
 *
 * @package Help
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the help_status taxonomy.
 *
 * @return void
 */
function help_register_taxonomies() {
	$labels = array(
		'name'                       => _x( 'Help Statuses', 'Taxonomy General Name', 'help-desk' ),
		'singular_name'              => _x( 'Help Status', 'Taxonomy Singular Name', 'help-desk' ),
		'menu_name'                  => __( 'Help Statuses', 'help-desk' ),
		'all_items'                  => __( 'All Help Statuses', 'help-desk' ),
		'parent_item'                => __( 'Parent Help Status', 'help-desk' ),
		'parent_item_colon'          => __( 'Parent Help Status:', 'help-desk' ),
		'new_item_name'              => __( 'New Help Status Name', 'help-desk' ),
		'add_new_item'               => __( 'Add New Help Status', 'help-desk' ),
		'edit_item'                  => __( 'Edit Help Status', 'help-desk' ),
		'update_item'                => __( 'Update Help Status', 'help-desk' ),
		'view_item'                  => __( 'View Help Status', 'help-desk' ),
		'separate_items_with_commas' => __( 'Separate help statuses with commas', 'help-desk' ),
		'add_or_remove_items'        => __( 'Add or remove help statuses', 'help-desk' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'help-desk' ),
		'popular_items'              => __( 'Popular Help Statuses', 'help-desk' ),
		'search_items'               => __( 'Search Help Statuses', 'help-desk' ),
		'not_found'                  => __( 'Not Found', 'help-desk' ),
		'no_terms'                   => __( 'No help statuses', 'help-desk' ),
		'items_list'                 => __( 'Help Statuses list', 'help-desk' ),
		'items_list_navigation'      => __( 'Help Statuses list navigation', 'help-desk' ),
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
		'rewrite'           => array( 'slug' => 'help-status' ),
	);

	register_taxonomy( 'help_status', array( 'help_ticket' ), $args );
}
add_action( 'init', 'help_register_taxonomies' );
