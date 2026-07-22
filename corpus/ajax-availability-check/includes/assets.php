<?php
/**
 * Enqueue frontend assets for Availability Check.
 *
 * @package Avch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function avch_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'availability' ) ) {
		return;
	}

	wp_enqueue_style(
		'avch-frontend-style',
		plugins_url( 'assets/css/frontend.css', AVCH_FILE ),
		array(),
		AVCH_VERSION
	);

	wp_enqueue_script(
		'avch-frontend-script',
		plugins_url( 'assets/js/frontend.js', AVCH_FILE ),
		array( 'jquery' ),
		AVCH_VERSION,
		true
	);

	wp_localize_script(
		'avch-frontend-script',
		'avchAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'avch_availability_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'avch_enqueue_assets' );
