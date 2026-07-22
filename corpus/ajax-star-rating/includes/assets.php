<?php
/**
 * Enqueue frontend assets for AJAX Star Rating.
 *
 * @package Arsr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function arsr_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'star_rating' ) ) {
		return;
	}

	wp_enqueue_style(
		'arsr-frontend-style',
		plugins_url( 'assets/css/frontend.css', ARSR_FILE ),
		array(),
		ARSR_VERSION
	);

	wp_enqueue_script(
		'arsr-frontend-script',
		plugins_url( 'assets/js/frontend.js', ARSR_FILE ),
		array( 'jquery' ),
		ARSR_VERSION,
		true
	);

	wp_localize_script(
		'arsr-frontend-script',
		'arsrAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'arsr_star_rating_nonce' ),
			'post_id'  => get_the_ID(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'arsr_enqueue_assets' );
