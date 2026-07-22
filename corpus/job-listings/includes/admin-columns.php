<?php
/**
 * Add admin columns for job CPT.
 *
 * @package Jobl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom columns to the jobs list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function jobl_add_job_columns( $columns ) {
	$columns = array(
		'cb'              => '<input type="checkbox" />',
		'title'           => _x( 'Job Title', 'column name', 'job-listings' ),
		'jobl_location'   => _x( 'Location', 'column name', 'job-listings' ),
		'jobl_employment' => _x( 'Employment Type', 'column name', 'job-listings' ),
		'jobl_salary'     => _x( 'Salary Range', 'column name', 'job-listings' ),
		'date'            => _x( 'Date', 'column name', 'job-listings' ),
	);
	return $columns;
}
add_filter( 'manage_job_posts_columns', 'jobl_add_job_columns' );

/**
 * Display custom column content.
 *
 * @param string $column_name The column name.
 * @param int    $post_id     The post ID.
 * @return void
 */
function jobl_display_job_column( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'jobl_location':
			$location = get_post_meta( $post_id, 'jobl_location', true );
			echo esc_html( $location );
			break;
		case 'jobl_employment':
			$employment = get_post_meta( $post_id, 'jobl_employment_type', true );
			echo esc_html( $employment );
			break;
		case 'jobl_salary':
			$salary = get_post_meta( $post_id, 'jobl_salary_range', true );
			echo esc_html( $salary );
			break;
	}
}
add_action( 'manage_job_posts_custom_column', 'jobl_display_job_column', 10, 2 );
