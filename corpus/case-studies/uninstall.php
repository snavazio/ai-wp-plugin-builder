<?php
/**
 * Uninstall cleanup for Case Studies.
 *
 * @package Cstuds
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all case-study posts and their meta.
$case_studies = get_posts(
	array(
		'post_type'      => 'case-study',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $case_studies as $case_study_id ) {
	wp_delete_post( $case_study_id, true );
}
