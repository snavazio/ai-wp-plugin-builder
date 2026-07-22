<?php
/**
 * Plugin Name:       Back To Top Button
 * Description:       Adds a customizable back-to-top button with settings for enablement and label text.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       back-to-top-button
 *
 * @package Bttb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BTTB_VERSION', '1.0.0' );
define( 'BTTB_FILE', __FILE__ );
define( 'BTTB_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function bttb_load_textdomain() {
	load_plugin_textdomain( 'back-to-top-button', false, dirname( plugin_basename( BTTB_FILE ) ) . '/languages' );
}
add_action( 'init', 'bttb_load_textdomain' );

// Include feature files.
require_once BTTB_PATH . 'includes/admin-page.php';

/**
 * Output the back-to-top button in the footer.
 *
 * @return void
 */
function bttb_output_button() {
	$settings = get_option(
		'bttb_settings',
		array(
			'enabled'     => true,
			'button_text' => __( 'Back to Top', 'back-to-top-button' ),
		)
	);

	// Skip if disabled.
	if ( ! $settings['enabled'] ) {
		return;
	}

	// Escape settings for output.
	?>
	<button id="bttb-scroll-top" class="bttb-scroll-top" aria-label="<?php esc_attr_e( 'Back to Top', 'back-to-top-button' ); ?>">
		<?php echo esc_html( $settings['button_text'] ); ?>
	</button>
	<script>
	document.getElementById('bttb-scroll-top').addEventListener('click', function() {
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'bttb_output_button' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function bttb_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bttb_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function bttb_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bttb_deactivate' );
