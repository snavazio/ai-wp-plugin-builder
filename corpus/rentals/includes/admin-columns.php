<?php
/**
 * Add admin columns for rental CPT.
 *
 * @package Rent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the rentals list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function rent_add_rental_columns( $columns ) {
	$columns = array(
		'cb'         => '<input type="checkbox" />',
		'title'      => _x( 'Rental Title', 'column name', 'rentals' ),
		'rent_price' => _x( 'Price', 'column name', 'rentals' ),
		'date'       => _x( 'Date', 'column name', 'rentals' ),
	);
	return $columns;
}
add_filter( 'manage_rental_posts_columns', 'rent_add_rental_columns' );

/**
 * Display custom column content.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function rent_display_rental_column( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'rent_price':
			$price = get_post_meta( $post_id, 'rent_price', true );
			echo esc_html( $price );
			break;
	}
}
add_action( 'manage_rental_posts_custom_column', 'rent_display_rental_column', 10, 2 );
