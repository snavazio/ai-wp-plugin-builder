<?php
/**
 * Enqueue frontend assets for AJAX Comment Vote.
 *
 * @package Acv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function acv1_enqueue_assets() {
	if ( ! is_singular() || ! comments_open() || get_comments_number() < 1 ) {
		return;
	}

	wp_enqueue_style(
		'acv1-frontend-style',
		plugins_url( 'assets/css/frontend.css', ACV1_FILE ),
		array(),
		ACV1_VERSION
	);

	wp_enqueue_script(
		'acv1-frontend-script',
		plugins_url( 'assets/js/frontend.js', ACV1_FILE ),
		array( 'jquery' ),
		ACV1_VERSION,
		true
	);

	wp_localize_script(
		'acv1-frontend-script',
		'acv1Ajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'acv_vote_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'acv1_enqueue_assets' );
