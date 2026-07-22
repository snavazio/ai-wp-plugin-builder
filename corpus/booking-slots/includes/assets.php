<?php
/**
 * Enqueue frontend assets.
 *
 * @package Bkslot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function bkslot_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'slots' ) ) {
		return;
	}

	wp_enqueue_style(
		'bkslot-frontend-style',
		plugins_url( 'assets/css/frontend.css', BKSLOT_FILE ),
		array(),
		BKSLOT_VERSION
	);

	wp_enqueue_script(
		'bkslot-frontend-script',
		plugins_url( 'assets/js/frontend.js', BKSLOT_FILE ),
		array( 'jquery' ),
		BKSLOT_VERSION,
		true
	);

	wp_localize_script(
		'bkslot-frontend-script',
		'bkslotAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'book_slot_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bkslot_enqueue_assets' );
