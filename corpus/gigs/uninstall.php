<?php
/**
 * Uninstall cleanup for Gigs.
 *
 * @package Gigc
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all gigs.
$gigs = get_posts(
	array(
		'post_type'      => 'gigc_gig',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $gigs as $gig_id ) {
	wp_delete_post( $gig_id, true );
}
