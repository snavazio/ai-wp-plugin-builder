<?php
/**
 * Add admin columns for event CPT.
 *
 * @package Evcal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the events list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function evcal_add_event_columns( $columns ) {
	$columns = array(
		'cb'               => '<input type="checkbox" />',
		'title'            => _x( 'Event Title', 'column name', 'events-calendar' ),
		'evcal_start_date' => _x( 'Start Date', 'column name', 'events-calendar' ),
		'evcal_location'   => _x( 'Location', 'column name', 'events-calendar' ),
		'date'             => _x( 'Date', 'column name', 'events-calendar' ),
	);
	return $columns;
}
add_filter( 'manage_event_posts_columns', 'evcal_add_event_columns' );

/**
 * Display custom column content.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function evcal_display_event_column( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'evcal_start_date':
			$start_date = get_post_meta( $post_id, 'evcal_start_date', true );
			echo esc_html( $start_date );
			break;
		case 'evcal_location':
			$location = get_post_meta( $post_id, 'evcal_location', true );
			echo esc_html( $location );
			break;
	}
}
add_action( 'manage_event_posts_custom_column', 'evcal_display_event_column', 10, 2 );
