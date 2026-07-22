<?php
/**
 * Plugin Name:       Help Desk
 * Description:       A ticketing system with hierarchical status tracking, admin columns, and REST API endpoints.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       help-desk
 *
 * @package Help
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HELP_VERSION', '1.0.0' );
define( 'HELP_FILE', __FILE__ );
define( 'HELP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function help_load_textdomain() {
	load_plugin_textdomain( 'help-desk', false, dirname( plugin_basename( HELP_FILE ) ) . '/languages' );
}
add_action( 'init', 'help_load_textdomain' );

require_once HELP_PATH . 'includes/post-types.php';
require_once HELP_PATH . 'includes/taxonomies.php';
require_once HELP_PATH . 'includes/shortcodes.php';
require_once HELP_PATH . 'includes/rest.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function help_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'help_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function help_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'help_deactivate' );
