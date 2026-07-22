<?php
/**
 * Enqueue frontend assets for AJAX Quick View.
 *
 * @package Aqv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function aqv1_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'quick_view' ) ) {
		return;
	}

	wp_enqueue_style(
		'aqv1-frontend-style',
		plugins_url( 'assets/css/frontend.css', AQV1_FILE ),
		array(),
		AQV1_VERSION
	);

	wp_enqueue_script(
		'aqv1-frontend-script',
		plugins_url( 'assets/js/frontend.js', AQV1_FILE ),
		array( 'jquery' ),
		AQV1_VERSION,
		true
	);

	wp_localize_script(
		'aqv1-frontend-script',
		'aqv1Ajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'quick_view_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'aqv1_enqueue_assets' );
