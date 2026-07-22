<?php
/**
 * Uninstall cleanup for Game Catalog.
 *
 * @package Gcat
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all game posts and their meta.
$game_posts = get_posts(
	array(
		'post_type'        => 'game',
		'post_status'      => 'any',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);

foreach ( $game_posts as $game_post_id ) {
	wp_delete_post( $game_post_id, true );
}
