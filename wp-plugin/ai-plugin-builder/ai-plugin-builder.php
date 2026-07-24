<?php
/**
 * Plugin Name:       AI Plugin Builder
 * Description:       Admin screen to generate security-audited WordPress plugins from a plain-English spec, via the AI WP Plugin Builder service.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ai-plugin-builder
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AIWPB_VERSION', '1.0.0' );
define( 'AIWPB_FILE', __FILE__ );
define( 'AIWPB_PATH', plugin_dir_path( __FILE__ ) );
define( 'AIWPB_URL', plugin_dir_url( __FILE__ ) );

require_once AIWPB_PATH . 'includes/class-aiwpb-client.php';
require_once AIWPB_PATH . 'includes/class-aiwpb-settings.php';
require_once AIWPB_PATH . 'includes/class-aiwpb-admin.php';
require_once AIWPB_PATH . 'includes/class-aiwpb-rest.php';

/**
 * Boot the plugin.
 *
 * @return void
 */
function aiwpb_bootstrap() {
	( new Aiwpb_Settings() )->register();
	( new Aiwpb_Admin() )->register();
	( new Aiwpb_Rest() )->register();
}
add_action( 'plugins_loaded', 'aiwpb_bootstrap' );
