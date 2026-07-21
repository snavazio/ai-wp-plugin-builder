<?php
/**
 * Admin columns for testimonials.
 *
 * @package Tmnl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the testimonials list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function tmnl_add_admin_columns( $columns ) {
	$new_columns = array();

	// Insert the rating column after the title.
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if ( 'title' === $key ) {
			$new_columns['tmnl_rating'] = __( 'Rating', 'testimonials' );
		}
	}

	return $new_columns;
}
add_filter( 'manage_tmnl_testimonial_posts_columns', 'tmnl_add_admin_columns' );

/**
 * Display the custom column content.
 *
 * @param string $column  The column name.
 * @param int    $post_id The post ID.
 * @return void
 */
function tmnl_display_admin_column( $column, $post_id ) {
	if ( 'tmnl_rating' === $column ) {
		$rating = get_post_meta( $post_id, 'tmnl_rating', true );
		if ( $rating ) {
			// Display rating as stars.
			$stars = str_repeat( '★', absint( $rating ) ) . str_repeat( '☆', 5 - absint( $rating ) );
			echo esc_html( $stars . ' (' . $rating . '/5)' );
		} else {
			echo esc_html__( 'No rating', 'testimonials' );
		}
	}
}
add_action( 'manage_tmnl_testimonial_posts_custom_column', 'tmnl_display_admin_column', 10, 2 );

/**
 * Make the rating column sortable.
 *
 * @param array $columns The sortable columns.
 * @return array The modified sortable columns.
 */
function tmnl_sortable_columns( $columns ) {
	$columns['tmnl_rating'] = 'tmnl_rating';
	return $columns;
}
add_filter( 'manage_edit-tmnl_testimonial_sortable_columns', 'tmnl_sortable_columns' );

/**
 * Handle the sorting of the rating column.
 *
 * @param WP_Query $query The query object.
 * @return void
 */
function tmnl_rating_column_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'tmnl_rating' === $orderby ) {
		$query->set( 'meta_key', 'tmnl_rating' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}
add_action( 'pre_get_posts', 'tmnl_rating_column_orderby' );
