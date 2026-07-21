<?php
/**
 * Enqueue frontend assets.
 *
 * @package Prtf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function prtf_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ( ! has_shortcode( $post->post_content, 'portfolio' ) && ! is_singular( 'prtf_portfolio' ) ) ) {
		return;
	}

	wp_enqueue_style(
		'prtf-portfolio-style',
		plugins_url( 'assets/css/portfolio.css', PRTF_FILE ),
		array(),
		PRTF_VERSION
	);

	wp_enqueue_script(
		'prtf-portfolio-script',
		plugins_url( 'assets/js/portfolio.js', PRTF_FILE ),
		array( 'jquery' ),
		PRTF_VERSION,
		true
	);

	wp_localize_script(
		'prtf-portfolio-script',
		'prtfAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'prtf_load_more_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'prtf_enqueue_assets' );
