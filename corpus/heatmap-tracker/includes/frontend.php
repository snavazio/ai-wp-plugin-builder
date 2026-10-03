<?php
/**
 * Front-end tracker, admin-bar node and overlay assets.
 *
 * @package Hmtrk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the tracker (visitors) or the overlay (administrators) on tracked singular pages.
 *
 * @return void
 */
function hmtrk_enqueue_assets() {
	if ( ! is_singular() ) {
		return;
	}
	$post_id = (int) get_queried_object_id();
	if ( ! hmtrk_is_tracked( $post_id ) ) {
		return;
	}

	if ( current_user_can( 'manage_options' ) ) {
		wp_enqueue_style( 'hmtrk-overlay', plugins_url( 'assets/overlay.css', HMTRK_FILE ), array(), HMTRK_VERSION );
		wp_enqueue_script( 'hmtrk-overlay', plugins_url( 'assets/overlay.js', HMTRK_FILE ), array(), HMTRK_VERSION, true );
		wp_localize_script(
			'hmtrk-overlay',
			'hmtrkOverlay',
			array(
				'url'     => esc_url_raw( rest_url( 'hmtrk/v1/results' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'postId'  => $post_id,
				'strings' => array(
					'desktop' => __( 'Desktop', 'heatmap-tracker' ),
					'tablet'  => __( 'Tablet', 'heatmap-tracker' ),
					'mobile'  => __( 'Mobile', 'heatmap-tracker' ),
					'scroll'  => __( 'Visitors reaching this depth', 'heatmap-tracker' ),
					'error'   => __( 'Could not load heatmap data.', 'heatmap-tracker' ),
				),
			)
		);
		return;
	}

	wp_enqueue_script( 'hmtrk-tracker', plugins_url( 'assets/tracker.js', HMTRK_FILE ), array(), HMTRK_VERSION, true );
	wp_localize_script(
		'hmtrk-tracker',
		'hmtrkTracker',
		array(
			'url'    => esc_url_raw( rest_url( 'hmtrk/v1/events' ) ),
			'postId' => $post_id,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'hmtrk_enqueue_assets' );

/**
 * Add the Heatmap node to the admin bar on tracked pages.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 * @return void
 */
function hmtrk_admin_bar( $bar ) {
	if ( is_admin() || ! current_user_can( 'manage_options' ) || ! is_singular() ) {
		return;
	}
	if ( ! hmtrk_is_tracked( (int) get_queried_object_id() ) ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'hmtrk-toggle',
			'title' => esc_html__( 'Heatmap', 'heatmap-tracker' ),
			'href'  => '#hmtrk-toggle',
		)
	);
}
add_action( 'admin_bar_menu', 'hmtrk_admin_bar', 100 );
