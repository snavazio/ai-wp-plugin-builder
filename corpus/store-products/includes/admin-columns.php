<?php
/**
 * Add admin columns for product CPT.
 *
 * @package Strp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the products list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function strp_add_product_columns( $columns ) {
	$columns = array(
		'cb'         => '<input type="checkbox" />',
		'title'      => _x( 'Product Title', 'column name', 'store-products' ),
		'strp_price' => _x( 'Price', 'column name', 'store-products' ),
		'date'       => _x( 'Date', 'column name', 'store-products' ),
	);
	return $columns;
}
add_filter( 'manage_product_posts_columns', 'strp_add_product_columns' );

/**
 * Display custom column content.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function strp_display_product_column( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'strp_price':
			$price = get_post_meta( $post_id, 'strp_price', true );
			echo esc_html( $price );
			break;
	}
}
add_action( 'manage_product_posts_custom_column', 'strp_display_product_column', 10, 2 );
