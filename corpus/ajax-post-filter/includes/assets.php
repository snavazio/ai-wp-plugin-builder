<?php
/**
 * Enqueue frontend assets for AJAX Post Filter.
 *
 * @package Apf1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function apf1_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'post_filter' ) ) {
		return;
	}

	wp_enqueue_style(
		'apf1-frontend-style',
		plugins_url( 'assets/css/frontend.css', APF1_FILE ),
		array(),
		APF1_VERSION
	);

	wp_enqueue_script(
		'apf1-frontend-script',
		plugins_url( 'assets/js/frontend.js', APF1_FILE ),
		array( 'jquery' ),
		APF1_VERSION,
		true
	);

	wp_localize_script(
		'apf1-frontend-script',
		'apf1Ajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'apf1_post_filter_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'apf1_enqueue_assets' );
