<?php
/**
 * Plugin Name:       Healthcheck Log
 * Description:       Schedule a daily WP-Cron event that records a heartbeat timestamp and the current published-post count into an option (keep the last 14). Register/clear on activation/deactivation. A [healthcheck] shortcode shows the latest entry, escaped. Escape all output, sanitize all input, and pair every state-changing action with a nonce AND a capability check.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       healthcheck-log
 *
 * @package Hlck
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HLCK_VERSION', '1.0.0' );
define( 'HLCK_FILE', __FILE__ );
define( 'HLCK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function hlck_load_textdomain() {
	load_plugin_textdomain( 'healthcheck-log', false, dirname( plugin_basename( HLCK_FILE ) ) . '/languages' );
}
add_action( 'init', 'hlck_load_textdomain' );

/**
 * Store a daily healthcheck snapshot.
 *
 * @return void
 */
function hlck_store_healthcheck_data() {
	$post_count = wp_count_posts( 'post' )->publish;
	$timestamp  = current_time( 'mysql' );

	$data      = get_option( 'hlck_healthcheck_data', array() );
	$new_entry = array(
		'timestamp'  => $timestamp,
		'post_count' => $post_count,
	);

	array_unshift( $data, $new_entry );
	if ( count( $data ) > 14 ) {
		$data = array_slice( $data, 0, 14 );
	}

	update_option( 'hlck_healthcheck_data', $data );
}

/**
 * Register the daily cron event.
 *
 * @return void
 */
function hlck_register_cron() {
	if ( ! wp_next_scheduled( 'healthcheck_daily' ) ) {
		wp_schedule_event( time(), 'daily', 'healthcheck_daily' );
	}
}
add_action( 'init', 'hlck_register_cron' );

/**
 * Handle the daily healthcheck cron event.
 *
 * @return void
 */
function hlck_handle_cron() {
	hlck_store_healthcheck_data();
}
add_action( 'healthcheck_daily', 'hlck_handle_cron' );

/**
 * Shortcode handler for [healthcheck].
 *
 * @return string
 */
function hlck_shortcode_healthcheck() {
	$data = get_option( 'hlck_healthcheck_data', array() );
	if ( empty( $data ) ) {
		return esc_html__( 'No healthcheck data available.', 'healthcheck-log' );
	}

	$latest = $data[0];
	ob_start();
	?>
	<div class="hlck-healthcheck">
		<p>
		<?php
		echo esc_html(
			sprintf(
			// translators: %1$s is the timestamp, %2$d is the post count.
				__( 'Latest healthcheck at %1$s: %2$d published posts', 'healthcheck-log' ),
				$latest['timestamp'],
				$latest['post_count']
			)
		);
		?>
		</p>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'healthcheck', 'hlck_shortcode_healthcheck' );

/**
 * Activation hook.
 *
 * @return void
 */
function hlck_activate() {
	hlck_register_cron();
}
register_activation_hook( __FILE__, 'hlck_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function hlck_deactivate() {
	$timestamp = wp_next_scheduled( 'healthcheck_daily' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'healthcheck_daily' );
	}
}
register_deactivation_hook( __FILE__, 'hlck_deactivate' );
