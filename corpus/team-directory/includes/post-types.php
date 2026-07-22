<?php
/**
 * Register custom post type for Team Members.
 *
 * @package Tdir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the tdir_team_member custom post type.
 *
 * @return void
 */
function tdir_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Team Members', 'Post Type General Name', 'team-directory' ),
		'singular_name'         => _x( 'Team Member', 'Post Type Singular Name', 'team-directory' ),
		'menu_name'             => __( 'Team Members', 'team-directory' ),
		'name_admin_bar'        => __( 'Team Member', 'team-directory' ),
		'archives'              => __( 'Team Member Archives', 'team-directory' ),
		'attributes'            => __( 'Team Member Attributes', 'team-directory' ),
		'parent_item_colon'     => __( 'Parent Team Member:', 'team-directory' ),
		'all_items'             => __( 'All Team Members', 'team-directory' ),
		'add_new_item'          => __( 'Add New Team Member', 'team-directory' ),
		'add_new'               => __( 'Add New', 'team-directory' ),
		'new_item'              => __( 'New Team Member', 'team-directory' ),
		'edit_item'             => __( 'Edit Team Member', 'team-directory' ),
		'update_item'           => __( 'Update Team Member', 'team-directory' ),
		'view_item'             => __( 'View Team Member', 'team-directory' ),
		'view_items'            => __( 'View Team Members', 'team-directory' ),
		'search_items'          => __( 'Search Team Members', 'team-directory' ),
		'not_found'             => __( 'Not found', 'team-directory' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'team-directory' ),
		'featured_image'        => __( 'Featured Image', 'team-directory' ),
		'set_featured_image'    => __( 'Set featured image', 'team-directory' ),
		'remove_featured_image' => __( 'Remove featured image', 'team-directory' ),
		'use_featured_image'    => __( 'Use as featured image', 'team-directory' ),
		'insert_into_item'      => __( 'Insert into team member', 'team-directory' ),
		'uploaded_to_this_item' => __( 'Uploaded to this team member', 'team-directory' ),
		'items_list'            => __( 'Team Members list', 'team-directory' ),
		'items_list_navigation' => __( 'Team Members list navigation', 'team-directory' ),
		'filter_items_list'     => __( 'Filter team members list', 'team-directory' ),
	);

	$args = array(
		'label'               => __( 'Team Member', 'team-directory' ),
		'description'         => __( 'Team members with department and contact information', 'team-directory' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-groups',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'tdir_team_member', $args );
}
add_action( 'init', 'tdir_register_post_types' );
