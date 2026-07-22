<?php
/**
 * Plugin Name:       FAQ Item Block
 * Description:       Dynamic server-rendered Gutenberg block for FAQ question/answer pairs.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       faq-item-block
 *
 * @package Faqb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FAQB_VERSION', '1.0.0' );
define( 'FAQB_FILE', __FILE__ );
define( 'FAQB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function faqb_load_textdomain() {
	load_plugin_textdomain( 'faq-item-block', false, dirname( plugin_basename( FAQB_FILE ) ) . '/languages' );
}
add_action( 'init', 'faqb_load_textdomain' );

/**
 * Register the FAQ Item Block.
 *
 * @return void
 */
function faqb_register_faq_item_block() {
	register_block_type(
		'faq-item-block/faq-item',
		array(
			'render_callback' => 'faqb_render_faq_item_block',
		)
	);
}
add_action( 'init', 'faqb_register_faq_item_block' );

/**
 * Render the FAQ Item Block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered block.
 */
function faqb_render_faq_item_block( $attributes ) {
	// Sanitize attributes.
	$question = isset( $attributes['question'] ) ? sanitize_text_field( $attributes['question'] ) : '';
	$answer   = isset( $attributes['answer'] ) ? sanitize_text_field( $attributes['answer'] ) : '';

	// Escape output.
	$output  = '<div class="faqb-faq-item">';
	$output .= '<div class="faqb-question">' . esc_html( $question ) . '</div>';
	$output .= '<div class="faqb-answer">' . esc_html( $answer ) . '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function faqb_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'faqb_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function faqb_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'faqb_deactivate' );
