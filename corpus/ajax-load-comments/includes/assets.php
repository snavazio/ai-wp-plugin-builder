<?php
/**
 * Enqueue frontend assets for AJAX Load Comments.
 *
 * @package Alcp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS.
 *
 * @return void
 */
function alcp_enqueue_assets() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'load_comments' ) ) {
		return;
	}

	wp_enqueue_style(
		'alcp-frontend-style',
		plugins_url( 'assets/css/frontend.css', ALCP_FILE ),
		array(),
		ALCP_VERSION
	);

	wp_enqueue_script(
		'alcp-frontend-script',
		plugins_url( 'assets/js/frontend.js', ALCP_FILE ),
		array( 'jquery' ),
		ALCP_VERSION,
		true
	);

	wp_localize_script(
		'alcp-frontend-script',
		'alcpAjax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'alcp_load_comments_nonce' ),
			'post_id'  => get_the_ID(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'alcp_enqueue_assets' );
