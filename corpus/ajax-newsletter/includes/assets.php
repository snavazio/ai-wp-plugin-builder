<?php
/**
 * Enqueue frontend assets for AJAX Newsletter.
 *
 * @package Anews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function anews_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'newsletter' ) ) {
		return;
	}

	wp_enqueue_style(
		'anews-frontend-style',
		plugins_url( 'assets/css/frontend.css', ANEWS_FILE ),
		array(),
		ANEWS_VERSION
	);

	wp_enqueue_script(
		'anews-frontend-script',
		plugins_url( 'assets/js/frontend.js', ANEWS_FILE ),
		array( 'jquery' ),
		ANEWS_VERSION,
		true
	);

	wp_localize_script(
		'anews-frontend-script',
		'anewsAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'anews_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'anews_enqueue_assets' );
