<?php
/**
 * Plugin Name:       Scroll To Top
 * Description:       Adds a scroll to top button in the footer with customizable settings.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       scroll-to-top
 *
 * @package Sttop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STTOP_VERSION', '1.0.0' );
define( 'STTOP_FILE', __FILE__ );
define( 'STTOP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function sttop_load_textdomain() {
	load_plugin_textdomain( 'scroll-to-top', false, dirname( plugin_basename( STTOP_FILE ) ) . '/languages' );
}
add_action( 'init', 'sttop_load_textdomain' );

// Include feature files.
require_once STTOP_PATH . 'includes/admin-page.php';

/**
 * Output the scroll to top button in the footer.
 *
 * @return void
 */
function sttop_output_scroll_to_top() {
	$settings = get_option(
		'sttop_settings',
		array(
			'button_text'  => __( 'Scroll to Top', 'scroll-to-top' ),
			'button_color' => '#000000',
		)
	);

	// Escape settings for output.
	$button_text  = esc_html( $settings['button_text'] );
	$button_color = esc_attr( $settings['button_color'] );
	?>
	<button id="sttop-scroll-top" class="sttop-scroll-top" style="background-color: <?php echo esc_attr( $settings['button_color'] ); ?>;" aria-label="<?php esc_attr_e( 'Scroll to top', 'scroll-to-top' ); ?>">
		<?php echo esc_html( $settings['button_text'] ); ?>
	</button>
	<script>
	document.getElementById('sttop-scroll-top').addEventListener('click', function() {
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'sttop_output_scroll_to_top' );

/**
 * Activation hook. Register rewrite-affecting features before flushing rules.
 *
 * @return void
 */
function sttop_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'sttop_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function sttop_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'sttop_deactivate' );
