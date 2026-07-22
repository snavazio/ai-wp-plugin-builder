<?php
/**
 * Enqueue frontend assets for Vote Poll.
 *
 * @package Vtpol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function vtpol_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'poll' ) ) {
		return;
	}

	wp_enqueue_style(
		'vtpol-frontend-style',
		plugins_url( 'assets/css/frontend.css', VTPOL_FILE ),
		array(),
		VTPOL_VERSION
	);

	wp_enqueue_script(
		'vtpol-frontend-script',
		plugins_url( 'assets/js/frontend.js', VTPOL_FILE ),
		array( 'jquery' ),
		VTPOL_VERSION,
		true
	);

	wp_localize_script(
		'vtpol-frontend-script',
		'vtpolAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'vtpol_vote_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'vtpol_enqueue_assets' );
