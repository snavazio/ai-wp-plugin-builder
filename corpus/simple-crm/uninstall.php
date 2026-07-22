<?php
/**
 * Uninstall cleanup for Simple CRM.
 *
 * @package Scrm
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all contact posts.
$contacts = get_posts(
	array(
		'post_type'        => 'scrm_contact',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $contacts as $contact_id ) {
	wp_delete_post( $contact_id, true );
}

// Delete all terms in the status taxonomy.
$status_terms = get_terms(
	array(
		'taxonomy'   => 'scrm_status',
		'hide_empty' => false,
	)
);

foreach ( $status_terms as $scrm_term ) {
	wp_delete_term( $scrm_term->term_id, 'scrm_status' );
}
