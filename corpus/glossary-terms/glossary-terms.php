<?php
/**
 * Plugin Name:       Glossary Terms
 * Description:       A custom post type for glossary terms with a shortcode to display them alphabetically.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       glossary-terms
 *
 * @package Gterm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GTERM_VERSION', '1.0.0' );
define( 'GTERM_FILE', __FILE__ );
define( 'GTERM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function gterm_load_textdomain() {
	load_plugin_textdomain( 'glossary-terms', false, dirname( plugin_basename( GTERM_FILE ) ) . '/languages' );
}
add_action( 'init', 'gterm_load_textdomain' );

require_once GTERM_PATH . 'includes/post-types.php';
require_once GTERM_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function gterm_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gterm_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function gterm_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gterm_deactivate' );
