<?php
/**
 * Enqueue frontend assets for Reaction Buttons.
 *
 * @package Reac
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function reac_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'reactions' ) ) {
		return;
	}

	wp_enqueue_style(
		'reac-frontend-style',
		plugins_url( 'assets/css/frontend.css', REAC_FILE ),
		array(),
		REAC_VERSION
	);

	wp_enqueue_script(
		'reac-frontend-script',
		plugins_url( 'assets/js/frontend.js', REAC_FILE ),
		array( 'jquery' ),
		REAC_VERSION,
		true
	);

	wp_localize_script(
		'reac-frontend-script',
		'reacAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'reac_reaction_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'reac_enqueue_assets' );
