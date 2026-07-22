<?php
/**
 * Enqueue frontend assets for FAQs accordion.
 *
 * @package Faqs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend CSS and JS for the FAQs accordion.
 *
 * @return void
 */
function faqs_enqueue_assets() {
	if ( ! is_singular( 'faqs_faq' ) && ! has_shortcode( get_the_content(), 'faqs' ) ) {
		return;
	}

	wp_enqueue_style(
		'faqs-accordion-style',
		plugins_url( 'assets/css/accordion.css', FAQS_FILE ),
		array(),
		FAQS_VERSION
	);

	wp_enqueue_script(
		'faqs-accordion-script',
		plugins_url( 'assets/js/accordion.js', FAQS_FILE ),
		array( 'jquery' ),
		FAQS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'faqs_enqueue_assets' );
