<?php
/**
 * Plugin Name:       Cookie Notice
 * Description:       Adds a dismissible cookie consent banner in the footer with customizable message and button label.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cookie-notice
 *
 * @package Cns1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CNS1_VERSION', '1.0.0' );
define( 'CNS1_FILE', __FILE__ );
define( 'CNS1_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function cns1_load_textdomain() {
	load_plugin_textdomain( 'cookie-notice', false, dirname( plugin_basename( CNS1_FILE ) ) . '/languages' );
}
add_action( 'init', 'cns1_load_textdomain' );

// Include feature files.
require_once CNS1_PATH . 'includes/admin-page.php';

/**
 * Output the cookie consent banner in the footer.
 *
 * @return void
 */
function cns1_output_cookie_banner() {
	// Get settings from options.
	$settings = get_option(
		'cns1_settings',
		array(
			'message'      => __( 'This website uses cookies to improve your experience. By continuing to use this site, you agree to our use of cookies.', 'cookie-notice' ),
			'button_label' => __( 'Accept', 'cookie-notice' ),
		)
	);

	// Only show if not dismissed.
	if ( isset( $_COOKIE['cns1_dismissed'] ) ) {
		return;
	}

	?>
	<div id="cns1-cookie-banner" class="cns1-cookie-banner" role="alert">
		<p><?php echo wp_kses_post( $settings['message'] ); ?></p>
		<button id="cns1-cookie-accept" class="cns1-cookie-accept" aria-label="<?php esc_attr_e( 'Accept cookies', 'cookie-notice' ); ?>">
			<?php echo esc_html( $settings['button_label'] ); ?>
		</button>
	</div>
	<script>
		document.getElementById('cns1-cookie-accept').addEventListener('click', function() {
			var d = new Date();
			d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
			document.cookie = 'cns1_dismissed=true; expires=' + d.toUTCString() + '; path=/';
			document.getElementById('cns1-cookie-banner').style.display = 'none';
		});
	</script>
	<?php
}
add_action( 'wp_footer', 'cns1_output_cookie_banner' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function cns1_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cns1_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function cns1_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cns1_deactivate' );
