<?php
/**
 * Enqueue frontend assets for Quick Search.
 *
 * @package Qsrch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function qsrch_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'quick_search' ) ) {
		return;
	}

	wp_enqueue_style(
		'qsrch-frontend-style',
		plugins_url( 'assets/css/frontend.css', QSRCH_FILE ),
		array(),
		QSRCH_VERSION
	);

	wp_enqueue_script(
		'qsrch-frontend-script',
		plugins_url( 'assets/js/frontend.js', QSRCH_FILE ),
		array( 'jquery' ),
		QSRCH_VERSION,
		true
	);

	wp_localize_script(
		'qsrch-frontend-script',
		'qsrchAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'quick_search_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'qsrch_enqueue_assets' );
