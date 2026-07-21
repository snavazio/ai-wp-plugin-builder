<?php
/**
 * Uninstall cleanup for the Client Notes sample.
 *
 * @package Good_Plugin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all client note posts and their meta.
$cnote_posts = get_posts(
	array(
		'post_type'        => 'cnote_note',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $cnote_posts as $cnote_post_id ) {
	wp_delete_post( $cnote_post_id, true );
}
