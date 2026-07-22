<?php
/**
 * Plugin Name:       Comics
 * Description:       Manages comics with custom post type, issue number meta, publisher and series taxonomies, and a shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       comics
 *
 * @package Comics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COMICS_VERSION', '1.0.0' );
define( 'COMICS_FILE', __FILE__ );
define( 'COMICS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function comics_load_textdomain() {
	load_plugin_textdomain( 'comics', false, dirname( plugin_basename( COMICS_FILE ) ) . '/languages' );
}
add_action( 'init', 'comics_load_textdomain' );

require_once COMICS_PATH . 'includes/post-types.php';
require_once COMICS_PATH . 'includes/taxonomies.php';
require_once COMICS_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function comics_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'comics_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function comics_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'comics_deactivate' );
