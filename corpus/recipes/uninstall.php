<?php
/**
 * Uninstall cleanup for Recipes.
 *
 * @package Rcpr
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete all recipe posts.
$recipes = get_posts(
	array(
		'post_type'      => 'recipe',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $recipes as $recipe_id ) {
	wp_delete_post( $recipe_id, true );
}
