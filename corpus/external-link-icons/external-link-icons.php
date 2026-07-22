<?php
/**
 * Plugin Name:       External Link Icons
 * Description:       Adds rel='noopener' and visual markers to external links in post content with a settings toggle.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       external-link-icons
 *
 * @package Elink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELINK_VERSION', '1.0.0' );
define( 'ELINK_FILE', __FILE__ );
define( 'ELINK_PATH', plugin_dir_path( __FILE__ ) );
define( 'ELINK_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function elink_load_textdomain() {
	load_plugin_textdomain( 'external-link-icons', false, dirname( plugin_basename( ELINK_FILE ) ) . '/languages' );
}
add_action( 'init', 'elink_load_textdomain' );

// Include feature files.
require_once ELINK_PATH . 'includes/admin-page.php';

/**
 * Enqueue frontend styles for external link icons.
 *
 * @return void
 */
function elink_enqueue_styles() {
	wp_enqueue_style( 'elink-frontend', ELINK_URL . 'external-link-icons.css', array(), ELINK_VERSION );
}
add_action( 'wp_enqueue_scripts', 'elink_enqueue_styles' );

/**
 * Add rel="noopener" and external link class to external links in content.
 *
 * @param string $content The post content.
 * @return string Modified content.
 */
function elink_add_external_link_icons( $content ) {
	if ( ! get_option( 'elink_enabled', false ) ) {
		return $content;
	}

	// Skip if content is empty or contains no links.
	if ( empty( $content ) || ! preg_match( '/<a\s+[^>]*href\s*=/i', $content ) ) {
		return $content;
	}

	// Process external links.
	$content = preg_replace_callback(
		'/<a\s+([^>]*href\s*=\s*["\']?([^"\'\s>]+)["\']?[^>]*)([^>]*)>/i',
		function ( $matches ) {
			$href = $matches[2];

			// Skip mailto and anchor links.
			if ( strpos( $href, 'mailto:' ) === 0 || strpos( $href, '#' ) === 0 ) {
				return $matches[0];
			}

			// Skip relative links.
			if ( ! preg_match( '/^https?:\/\//i', $href ) ) {
				return $matches[0];
			}

			// Skip internal links.
			$parsed = parse_url( $href );
			if ( ! $parsed || ! isset( $parsed['host'] ) ) {
				return $matches[0];
			}
			$host           = preg_replace( '/^www\./', '', $parsed['host'] );
			$current_domain = preg_replace( '/^www\./', '', parse_url( home_url(), PHP_URL_HOST ) );
			if ( $host === $current_domain ) {
				return $matches[0];
			}

			// Add rel="noopener" if not present.
			$rel = '';
			if ( preg_match( '/rel\s*=\s*["\']?([^"\'\s>]+)/i', $matches[1], $rel_match ) ) {
				$rel = $rel_match[1];
				if ( ! preg_match( '/noopener/i', $rel ) ) {
					$rel .= ' noopener';
				}
			} else {
				$rel = 'noopener';
			}

			// Replace or add rel attribute.
			$matches[1] = preg_replace( '/rel\s*=\s*["\']?[^"\'\s>]+["\']?/', '', $matches[1] );
			$matches[1] = trim( $matches[1] );
			if ( $matches[1] ) {
				$matches[1] .= ' ';
			}
			$matches[1] .= 'rel="' . esc_attr( $rel ) . '"';

			// Add external-link class.
			$matches[1] = preg_replace( '/class\s*=\s*["\']?([^"\'\s>]+)/i', 'class="$1 external-link"', $matches[1] );
			if ( ! preg_match( '/class\s*=/i', $matches[1] ) ) {
				$matches[1] .= ' class="external-link"';
			}

			return '<a ' . $matches[1] . $matches[3] . '>';
		},
		$content
	);

	return $content;
}
add_filter( 'the_content', 'elink_add_external_link_icons', 10, 1 );

/**
 * Register the External Link Icons settings page.
 *
 * @return void
 */
function elink_register_admin_page() {
	add_options_page(
		__( 'External Link Icons', 'external-link-icons' ),
		__( 'External Link Icons', 'external-link-icons' ),
		'manage_options',
		'external-link-icons-settings',
		'elink_admin_page_callback'
	);
}
add_action( 'admin_menu', 'elink_register_admin_page' );
