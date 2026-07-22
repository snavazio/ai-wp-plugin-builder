<?php
/**
 * Enqueue frontend assets.
 *
 * @package Lmpa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function lmpa_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'load_more' ) ) {
		return;
	}

	wp_enqueue_style(
		'lmpa-frontend-style',
		plugins_url( 'assets/css/frontend.css', LMPA_FILE ),
		array(),
		LMPA_VERSION
	);

	wp_enqueue_script(
		'lmpa-frontend-script',
		plugins_url( 'assets/js/frontend.js', LMPA_FILE ),
		array( 'jquery' ),
		LMPA_VERSION,
		true
	);

	wp_localize_script(
		'lmpa-frontend-script',
		'lmpaAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'lmpa_load_more_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'lmpa_enqueue_assets' );
