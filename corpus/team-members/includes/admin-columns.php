<?php
/**
 * Admin columns for team members.
 *
 * @package Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the team members list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function team_add_admin_columns( $columns ) {
	$new_columns = array();

	// Insert the role column after the title.
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if ( 'title' === $key ) {
			$new_columns['team_role'] = __( 'Role', 'team-members' );
		}
	}

	return $new_columns;
}
add_filter( 'manage_team_member_posts_columns', 'team_add_admin_columns' );

/**
 * Display the custom column content.
 *
 * @param string $column  The column name.
 * @param int    $post_id The post ID.
 * @return void
 */
function team_display_admin_column( $column, $post_id ) {
	if ( 'team_role' === $column ) {
		$role = get_post_meta( $post_id, 'team_role', true );
		if ( $role ) {
			echo esc_html( $role );
		} else {
			echo esc_html__( 'No role', 'team-members' );
		}
	}
}
add_action( 'manage_team_member_posts_custom_column', 'team_display_admin_column', 10, 2 );
