<?php
/**
 * Plugin Name:       AJAX Comment Vote
 * Description:       Adds an upvote button to comments with AJAX voting, incrementing comment meta counts securely.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-comment-vote
 *
 * @package Acv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACV1_VERSION', '1.0.0' );
define( 'ACV1_FILE', __FILE__ );
define( 'ACV1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function acv1_load_textdomain() {
	load_plugin_textdomain( 'ajax-comment-vote', false, dirname( plugin_basename( ACV1_FILE ) ) . '/languages' );
}
add_action( 'init', 'acv1_load_textdomain' );

// Include necessary files.
require_once ACV1_PATH . 'includes/ajax.php';
require_once ACV1_PATH . 'includes/assets.php';
require_once ACV1_PATH . 'includes/comment.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function acv1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'acv1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function acv1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'acv1_deactivate' );
