<?php
/**
 * Plugin Name:       Social Share Buttons
 * Description:       Adds social share buttons to single posts with customizable network selection via admin settings.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       social-share-buttons
 *
 * @package Ssbs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SSBS_VERSION', '1.0.0' );
define( 'SSBS_FILE', __FILE__ );
define( 'SSBS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Get available social networks.
 *
 * @return array Array of network data.
 */
function ssbs_get_available_networks() {
	return array(
		'facebook'  => array(
			'label' => 'Facebook',
			'url'   => 'https://www.facebook.com/sharer/sharer.php?u={url}',
		),
		'twitter'   => array(
			'label' => 'Twitter',
			'url'   => 'https://twitter.com/intent/tweet?url={url}&text={title}',
		),
		'linkedin'  => array(
			'label' => 'LinkedIn',
			'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
		),
		'pinterest' => array(
			'label' => 'Pinterest',
			'url'   => 'https://pinterest.com/pin/create/button/?url={url}&media={image}&description={title}',
		),
		'email'     => array(
			'label' => 'Email',
			'url'   => 'mailto:?subject=Check%20out%20this%20post&body={title}%20{url}',
		),
	);
}

/**
 * Append social share buttons to content on single posts.
 *
 * @param string $content The post content.
 * @return string The content with share buttons appended.
 */
function ssbs_append_share_links( $content ) {
	if ( ! is_single() ) {
		return $content;
	}

	$selected_networks  = explode( ',', get_option( 'ssbs_social_networks', '' ) );
	$available_networks = ssbs_get_available_networks();

	if ( empty( $selected_networks ) || '0' === $selected_networks[0] ) {
		return $content;
	}

	$url   = get_permalink();
	$title = get_the_title();

	$share_buttons = '<div class="ssbs-share-buttons">';
	foreach ( $selected_networks as $network ) {
		if ( ! isset( $available_networks[ $network ] ) ) {
			continue;
		}
		$network_data   = $available_networks[ $network ];
		$network_url    = str_replace( '{url}', rawurlencode( $url ), $network_data['url'] );
		$network_url    = str_replace( '{title}', rawurlencode( $title ), $network_url );
		$share_buttons .= '<a href="' . esc_url( $network_url ) . '" target="_blank" rel="noopener" class="ssbs-share-button ssbs-share-button-' . esc_attr( $network ) . '">' . esc_html( $network_data['label'] ) . '</a>';
	}
	$share_buttons .= '</div>';

	return $content . $share_buttons;
}
add_filter( 'the_content', 'ssbs_append_share_links' );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function ssbs_load_textdomain() {
	load_plugin_textdomain( 'social-share-buttons', false, dirname( plugin_basename( SSBS_FILE ) ) . '/languages' );
}
add_action( 'init', 'ssbs_load_textdomain' );

// Load admin settings.
require_once SSBS_PATH . 'includes/admin-page.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function ssbs_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ssbs_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function ssbs_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ssbs_deactivate' );
