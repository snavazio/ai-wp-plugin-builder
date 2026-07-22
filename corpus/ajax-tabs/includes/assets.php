<?php
/**
 * Enqueue frontend assets for AJAX Tabs.
 *
 * @package Ajxt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function ajxt_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'tabs' ) ) {
		return;
	}

	wp_enqueue_style(
		'ajxt-frontend-style',
		plugins_url( 'assets/css/frontend.css', AJXT_FILE ),
		array(),
		AJXT_VERSION
	);

	wp_enqueue_script(
		'ajxt-frontend-script',
		plugins_url( 'assets/js/frontend.js', AJXT_FILE ),
		array( 'jquery' ),
		AJXT_VERSION,
		true
	);

	wp_localize_script(
		'ajxt-frontend-script',
		'ajxtAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ajxt_tabs_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ajxt_enqueue_assets' );
