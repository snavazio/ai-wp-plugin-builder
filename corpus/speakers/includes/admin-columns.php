<?php
/**
 * Add admin columns for speaker CPT.
 *
 * @package Spkrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the speakers list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function spkrs_add_speaker_columns( $columns ) {
	$columns = array(
		'cb'            => '<input type="checkbox" />',
		'title'         => _x( 'Name', 'column name', 'speakers' ),
		'spkrs_twitter' => _x( 'Twitter', 'column name', 'speakers' ),
		'date'          => _x( 'Date', 'column name', 'speakers' ),
	);
	return $columns;
}
add_filter( 'manage_speaker_posts_columns', 'spkrs_add_speaker_columns' );

/**
 * Display custom column content.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function spkrs_display_speaker_column( $column_name, $post_id ) {
	if ( 'spkrs_twitter' === $column_name ) {
		$twitter_handle = get_post_meta( $post_id, 'spkrs_twitter_handle', true );
		if ( $twitter_handle ) {
			echo esc_html( $twitter_handle );
		}
	}
}
add_action( 'manage_speaker_posts_custom_column', 'spkrs_display_speaker_column', 10, 2 );
