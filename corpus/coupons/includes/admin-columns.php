<?php
/**
 * Add admin columns for Coupons.
 *
 * @package Cpns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add 'Code' column to the Coupons admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function cpns_add_code_column( $columns ) {
	$columns['cpns_code'] = __( 'Code', 'coupons' );
	return $columns;
}
add_filter( 'manage_coupon_posts_columns', 'cpns_add_code_column' );

/**
 * Add 'Expiry' column to the Coupons admin list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function cpns_add_expiry_column( $columns ) {
	$columns['cpns_expiry'] = __( 'Expiry', 'coupons' );
	return $columns;
}
add_filter( 'manage_coupon_posts_columns', 'cpns_add_expiry_column' );

/**
 * Display coupon code in the 'Code' column.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function cpns_display_code_column( $column_name, $post_id ) {
	if ( 'cpns_code' === $column_name ) {
		$code = get_post_meta( $post_id, 'cpns_code', true );
		echo esc_html( $code );
	}
}
add_action( 'manage_coupon_posts_custom_column', 'cpns_display_code_column', 10, 2 );

/**
 * Display coupon expiry date in the 'Expiry' column.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function cpns_display_expiry_column( $column_name, $post_id ) {
	if ( 'cpns_expiry' === $column_name ) {
		$expiry = get_post_meta( $post_id, 'cpns_expiry', true );
		echo esc_html( $expiry );
	}
}
add_action( 'manage_coupon_posts_custom_column', 'cpns_display_expiry_column', 10, 2 );
