<?php
/**
 * Plugin Name:       Wine Catalog
 * Description:       A Wine CPT with a vintage meta (secure meta box), a hierarchical Region taxonomy and a Varietal taxonomy, and a [wines region="" varietal=""] shortcode filtering published wines, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check. Remove posts and terms on uninstall.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wine-catalog
 *
 * @package Winec
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WINEC_VERSION', '1.0.0' );
define( 'WINEC_FILE', __FILE__ );
define( 'WINEC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function winec_load_textdomain() {
	load_plugin_textdomain( 'wine-catalog', false, dirname( plugin_basename( WINEC_FILE ) ) . '/languages' );
}
add_action( 'init', 'winec_load_textdomain' );

require_once WINEC_PATH . 'includes/post-types.php';
require_once WINEC_PATH . 'includes/taxonomies.php';
require_once WINEC_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function winec_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'winec_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function winec_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'winec_deactivate' );
