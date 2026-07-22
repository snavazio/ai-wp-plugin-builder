<?php
/**
 * Add admin columns for employee CPT.
 *
 * @package Empc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the employees list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function empc_add_employee_columns( $columns ) {
	$columns = array(
		'cb'              => '<input type="checkbox" />',
		'title'           => _x( 'Name', 'column name', 'employees' ),
		'empc_department' => _x( 'Department', 'column name', 'employees' ),
		'empc_email'      => _x( 'Email', 'column name', 'employees' ),
		'date'            => _x( 'Date', 'column name', 'employees' ),
	);
	return $columns;
}
add_filter( 'manage_employee_posts_columns', 'empc_add_employee_columns' );

/**
 * Display custom column content.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function empc_display_employee_column( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'empc_department':
			$department = get_post_meta( $post_id, 'empc_department', true );
			echo esc_html( $department );
			break;
		case 'empc_email':
			$email = get_post_meta( $post_id, 'empc_email', true );
			echo esc_html( $email );
			break;
	}
}
add_action( 'manage_employee_posts_custom_column', 'empc_display_employee_column', 10, 2 );
