<?php
/**
 * Plugin Name:       Team Members
 * Description:       A Team Member CPT (title = name, editor = bio) with meta fields for role and email, editable in a secure meta box, plus an admin Role column. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check. Clean up on uninstall.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       team-members
 *
 * @package Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEAM_VERSION', '1.0.0' );
define( 'TEAM_FILE', __FILE__ );
define( 'TEAM_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function team_load_textdomain() {
	load_plugin_textdomain( 'team-members', false, dirname( plugin_basename( TEAM_FILE ) ) . '/languages' );
}
add_action( 'init', 'team_load_textdomain' );

// Include feature files.
require_once TEAM_PATH . 'includes/post-type.php';
require_once TEAM_PATH . 'includes/meta-box.php';
require_once TEAM_PATH . 'includes/admin-columns.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function team_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'team_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function team_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'team_deactivate' );
