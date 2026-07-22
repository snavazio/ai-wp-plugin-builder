<?php
/**
 * Plugin Name:       Announcement Bar
 * Description:       Adds a configurable announcement bar at the top of the site with a settings page for message and toggle.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       announcement-bar
 *
 * @package Annbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNBAR_VERSION', '1.0.0' );
define( 'ANNBAR_FILE', __FILE__ );
define( 'ANNBAR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function annbar_load_textdomain() {
	load_plugin_textdomain( 'announcement-bar', false, dirname( plugin_basename( ANNBAR_FILE ) ) . '/languages' );
}
add_action( 'init', 'annbar_load_textdomain' );

// Include feature files.
require_once ANNBAR_PATH . 'includes/admin-page.php';

/**
 * Output the announcement bar in the body.
 *
 * @return void
 */
function annbar_output_bar() {
	$settings = get_option(
		'annbar_settings',
		array(
			'enabled' => true,
			'message' => '',
		)
	);

	// Skip if disabled.
	if ( ! $settings['enabled'] ) {
		return;
	}

	// Escape message at point of output using wp_kses_post.
	?>
	<div id="annbar-announcement" class="annbar-announcement" role="alert">
		<?php echo wp_kses_post( $settings['message'] ); ?>
	</div>
	<?php
}
add_action( 'wp_body_open', 'annbar_output_bar' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function annbar_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'annbar_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function annbar_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'annbar_deactivate' );
