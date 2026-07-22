<?php
/**
 * Plugin Name:       Portfolio Skills
 * Description:       Work CPT with non-hierarchical Skill taxonomy (tags) and work_grid shortcode for filtering published works.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       portfolio-skills
 *
 * @package Pskl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PSKL_VERSION', '1.0.0' );
define( 'PSKL_FILE', __FILE__ );
define( 'PSKL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function pskl_load_textdomain() {
	load_plugin_textdomain( 'portfolio-skills', false, dirname( plugin_basename( PSKL_FILE ) ) . '/languages' );
}
add_action( 'init', 'pskl_load_textdomain' );

require_once PSKL_PATH . 'includes/post-types.php';
require_once PSKL_PATH . 'includes/taxonomies.php';
require_once PSKL_PATH . 'includes/shortcode.php';

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function pskl_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pskl_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function pskl_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pskl_deactivate' );
