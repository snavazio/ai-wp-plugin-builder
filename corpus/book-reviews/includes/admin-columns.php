<?php
/**
 * Admin columns for book reviews.
 *
 * @package Brpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the book reviews list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function brpl_add_admin_columns( $columns ) {
	$new_columns = array();

	// Insert the rating column after the title.
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if ( 'title' === $key ) {
			$new_columns['brpl_rating'] = __( 'Rating', 'book-reviews' );
		}
	}

	return $new_columns;
}
add_filter( 'manage_book_review_posts_columns', 'brpl_add_admin_columns' );

/**
 * Display the custom column content.
 *
 * @param string $column  The column name.
 * @param int    $post_id The post ID.
 * @return void
 */
function brpl_display_admin_column( $column, $post_id ) {
	if ( 'brpl_rating' === $column ) {
		$rating = get_post_meta( $post_id, 'brpl_rating', true );
		if ( $rating ) {
			// Display rating as stars.
			$stars = str_repeat( '★', absint( $rating ) ) . str_repeat( '☆', 5 - absint( $rating ) );
			echo esc_html( $stars . ' (' . $rating . '/5)' );
		} else {
			echo esc_html__( 'No rating', 'book-reviews' );
		}
	}
}
add_action( 'manage_book_review_posts_custom_column', 'brpl_display_admin_column', 10, 2 );

/**
 * Make the rating column sortable.
 *
 * @param array $columns The sortable columns.
 * @return array The modified sortable columns.
 */
function brpl_sortable_columns( $columns ) {
	$columns['brpl_rating'] = 'brpl_rating';
	return $columns;
}
add_filter( 'manage_edit-book_review_sortable_columns', 'brpl_sortable_columns' );

/**
 * Handle the sorting of the rating column.
 *
 * @param WP_Query $query The query object.
 * @return void
 */
function brpl_rating_column_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'brpl_rating' === $orderby ) {
		$query->set( 'meta_key', 'brpl_rating' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}
add_action( 'pre_get_posts', 'brpl_rating_column_orderby' );
