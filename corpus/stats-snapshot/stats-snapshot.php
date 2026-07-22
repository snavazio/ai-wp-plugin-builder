<?php
/**
 * Plugin Name:       Stats Snapshot
 * Description:       Stores daily snapshots of published post and comment counts, accessible via shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       stats-snapshot
 *
 * @package Stats
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STATS_VERSION', '1.0.0' );
define( 'STATS_FILE', __FILE__ );
define( 'STATS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function stats_load_textdomain() {
	load_plugin_textdomain( 'stats-snapshot', false, dirname( plugin_basename( STATS_FILE ) ) . '/languages' );
}
add_action( 'init', 'stats_load_textdomain' );

/**
 * Store a daily snapshot of post and comment counts.
 *
 * @return void
 */
function stats_snapshot_store() {
	$post_count    = wp_count_posts( 'post' )->publish;
	$comment_count = wp_count_comments()->approved;

	$data         = get_option( 'stats_snapshot_data', array() );
	$new_snapshot = array(
		'date'          => gmdate( 'Y-m-d' ),
		'post_count'    => $post_count,
		'comment_count' => $comment_count,
	);

	array_unshift( $data, $new_snapshot );
	if ( count( $data ) > 30 ) {
		$data = array_slice( $data, 0, 30 );
	}

	update_option( 'stats_snapshot_data', $data );
}

/**
 * Register the daily cron event.
 *
 * @return void
 */
function stats_register_cron() {
	if ( ! wp_next_scheduled( 'stats_snapshot_daily' ) ) {
		wp_schedule_event( time(), 'daily', 'stats_snapshot_daily' );
	}
}
add_action( 'init', 'stats_register_cron' );

/**
 * Handle the daily snapshot cron event.
 *
 * @return void
 */
function stats_handle_cron() {
	stats_snapshot_store();
}
add_action( 'stats_snapshot_daily', 'stats_handle_cron' );

/**
 * Shortcode handler for [stats_snapshot].
 *
 * @return string
 */
function stats_snapshot_shortcode() {
	$data = get_option( 'stats_snapshot_data', array() );
	if ( empty( $data ) ) {
		return esc_html__( 'No snapshots available.', 'stats-snapshot' );
	}

	ob_start();
	?>
	<table class="stats-snapshot-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'stats-snapshot' ); ?></th>
				<th><?php esc_html_e( 'Published Posts', 'stats-snapshot' ); ?></th>
				<th><?php esc_html_e( 'Comments', 'stats-snapshot' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $data as $snapshot ) : ?>
				<tr>
					<td><?php echo esc_html( $snapshot['date'] ); ?></td>
					<td><?php echo esc_html( $snapshot['post_count'] ); ?></td>
					<td><?php echo esc_html( $snapshot['comment_count'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	return ob_get_clean();
}
add_shortcode( 'stats_snapshot', 'stats_snapshot_shortcode' );

/**
 * Activation hook.
 *
 * @return void
 */
function stats_activate() {
	stats_register_cron();
}
register_activation_hook( __FILE__, 'stats_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function stats_deactivate() {
	$timestamp = wp_next_scheduled( 'stats_snapshot_daily' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'stats_snapshot_daily' );
	}
}
register_deactivation_hook( __FILE__, 'stats_deactivate' );
