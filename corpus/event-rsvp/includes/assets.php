<?php
/**
 * Enqueue frontend assets.
 *
 * @package Ersv
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function ersv_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'event_rsvp' ) ) {
		return;
	}

	wp_enqueue_style(
		'ersv-frontend-style',
		plugins_url( 'assets/css/frontend.css', ERSV_FILE ),
		array(),
		ERSV_VERSION
	);

	wp_enqueue_script(
		'ersv-frontend-script',
		plugins_url( 'assets/js/frontend.js', ERSV_FILE ),
		array( 'jquery' ),
		ERSV_VERSION,
		true
	);

	wp_localize_script(
		'ersv-frontend-script',
		'ersvAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ersv_rsvp_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ersv_enqueue_assets' );
