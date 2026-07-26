<?php
/**
 * Plugin Name:       Resume Filter Analyzer
 * Description:       Analyze resumes against job descriptions to simulate how hiring company filters evaluate candidates.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       resume-filter-analyzer
 *
 * @package Rfa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RFA_VERSION', '1.0.0' );
define( 'RFA_FILE', __FILE__ );
define( 'RFA_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rfa_load_textdomain() {
	load_plugin_textdomain( 'resume-filter-analyzer', false, dirname( plugin_basename( RFA_FILE ) ) . '/languages' );
}
add_action( 'init', 'rfa_load_textdomain' );

// Load shortcode.
require_once RFA_PATH . 'includes/shortcode-analyzer.php';

// Load REST API endpoints.
require_once RFA_PATH . 'includes/rest-api.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function rfa_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rfa_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rfa_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rfa_deactivate' );
