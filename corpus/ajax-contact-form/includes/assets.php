<?php
/**
 * Enqueue frontend assets.
 *
 * @package Acff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function acff_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'contact_form' ) ) {
		return;
	}

	wp_enqueue_style(
		'acff-frontend-style',
		plugins_url( 'assets/css/frontend.css', ACFF_FILE ),
		array(),
		ACFF_VERSION
	);

	wp_enqueue_script(
		'acff-frontend-script',
		plugins_url( 'assets/js/frontend.js', ACFF_FILE ),
		array( 'jquery' ),
		ACFF_VERSION,
		true
	);

	wp_localize_script(
		'acff-frontend-script',
		'acffAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'contact_form_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'acff_enqueue_assets' );
