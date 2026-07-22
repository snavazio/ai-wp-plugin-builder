<?php
/**
 * Register team member custom post type.
 *
 * @package Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the team_member post type.
 *
 * @return void
 */
function team_register_post_type() {
	$labels = array(
		'name'                  => _x( 'Team Members', 'Post type general name', 'team-members' ),
		'singular_name'         => _x( 'Team Member', 'Post type singular name', 'team-members' ),
		'menu_name'             => _x( 'Team Members', 'Admin Menu text', 'team-members' ),
		'name_admin_bar'        => _x( 'Team Member', 'Add New on Toolbar', 'team-members' ),
		'add_new'               => __( 'Add New', 'team-members' ),
		'add_new_item'          => __( 'Add New Team Member', 'team-members' ),
		'new_item'              => __( 'New Team Member', 'team-members' ),
		'edit_item'             => __( 'Edit Team Member', 'team-members' ),
		'view_item'             => __( 'View Team Member', 'team-members' ),
		'all_items'             => __( 'All Team Members', 'team-members' ),
		'search_items'          => __( 'Search Team Members', 'team-members' ),
		'parent_item_colon'     => __( 'Parent Team Member:', 'team-members' ),
		'not_found'             => __( 'No team members found.', 'team-members' ),
		'not_found_in_trash'    => __( 'No team members found in Trash.', 'team-members' ),
		'archives'              => _x( 'Team Member archives', 'The post type archive label used in nav menus.', 'team-members' ),
		'insert_into_item'      => _x( 'Insert into team member', 'Overrides the "Insert into post"/"Insert into page" phrase.', 'team-members' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this team member', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase.', 'team-members' ),
		'filter_items_list'     => _x( 'Filter team members list', 'Screen reader text for the filter links heading on the post type listing screen.', 'team-members' ),
		'items_list_navigation' => _x( 'Team members list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'team-members' ),
		'items_list'            => _x( 'Team members list', 'Screen reader text for the items list heading on the post type listing screen.', 'team-members' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-groups',
		'supports'           => array( 'title', 'editor' ),
		'show_in_rest'       => false,
	);

	register_post_type( 'team_member', $args );
}
add_action( 'init', 'team_register_post_type' );
