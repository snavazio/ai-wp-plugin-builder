<?php
/**
 * Uninstall cleanup for Team Members.
 *
 * @package Team
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all team_member posts and their meta.
$team_posts = get_posts(
	array(
		'post_type'        => 'team_member',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $team_posts as $team_post_id ) {
	wp_delete_post( $team_post_id, true );
}
