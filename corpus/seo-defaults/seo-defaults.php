<?php
/**
 * Plugin Name:       SEO Defaults
 * Description:       Adds a default meta description field and outputs it in wp_head when missing.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       seo-defaults
 *
 * @package Seod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEOD_VERSION', '1.0.0' );
define( 'SEOD_FILE', __FILE__ );
define( 'SEOD_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function seod_load_textdomain() {
	load_plugin_textdomain( 'seo-defaults', false, dirname( plugin_basename( SEOD_FILE ) ) . '/languages' );
}
add_action( 'init', 'seod_load_textdomain' );

// Load admin settings.
require_once SEOD_PATH . 'includes/admin-page.php';

/**
 * Output default meta description in wp_head.
 *
 * @return void
 */
function seo_default_meta_description() {
	$description = get_option( 'seod_default_meta_description', '' );
	if ( ! empty( $description ) ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'seo_default_meta_description' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function seod_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'seod_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function seod_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'seod_deactivate' );
