<?php
/**
 * Add admin columns for Case Studies.
 *
 * @package Cstuds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add 'Client' column to the Case Studies admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function cstuds_add_client_column( $columns ) {
	$columns['client'] = __( 'Client', 'case-studies' );
	return $columns;
}
add_filter( 'manage_case-study_posts_columns', 'cstuds_add_client_column' );

/**
 * Display client data in the 'Client' column.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function cstuds_display_client_column( $column_name, $post_id ) {
	if ( 'client' === $column_name ) {
		$client = get_post_meta( $post_id, 'cstuds_client', true );
		echo esc_html( $client );
	}
}
add_action( 'manage_case-study_posts_custom_column', 'cstuds_display_client_column', 10, 2 );
