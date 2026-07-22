<?php
/**
 * Plugin Name:       Reminder Log
 * Description:       Schedules daily cron to log posts scheduled for next 7 days, with shortcode display.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Stephen Navazio Agency
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       reminder-log
 *
 * @package Rlog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RLOG_VERSION', '1.0.0' );
define( 'RLOG_FILE', __FILE__ );
define( 'RLOG_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function rlog_load_textdomain() {
	load_plugin_textdomain( 'reminder-log', false, dirname( plugin_basename( RLOG_FILE ) ) . '/languages' );
}
add_action( 'init', 'rlog_load_textdomain' );

/**
 * Store daily log of posts scheduled for next 7 days.
 *
 * @return void
 */
function rlog_daily_log() {
	$after  = gmdate( 'Y-m-d' );
	$before = gmdate( 'Y-m-d', strtotime( '+7 days' ) );

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'future',
		'date_query'     => array(
			array(
				'after'     => $after,
				'before'    => $before,
				'inclusive' => true,
			),
		),
		'fields'         => 'ids',
		'posts_per_page' => -1,
	);

	$post_ids = get_posts( $args );

	$data    = get_option( 'rlog_scheduled_posts', array() );
	$new_log = array(
		'date'  => $after,
		'posts' => $post_ids,
	);

	array_unshift( $data, $new_log );
	if ( count( $data ) > 30 ) {
		$data = array_slice( $data, 0, 30 );
	}

	update_option( 'rlog_scheduled_posts', $data );
}

/**
 * Register the daily cron event.
 *
 * @return void
 */
function rlog_register_cron() {
	if ( ! wp_next_scheduled( 'rl_daily_log' ) ) {
		wp_schedule_event( time(), 'daily', 'rl_daily_log' );
	}
}
add_action( 'init', 'rlog_register_cron' );

/**
 * Shortcode handler for [reminder_log].
 *
 * @return string
 */
function rlog_shortcode() {
	$data = get_option( 'rlog_scheduled_posts', array() );
	if ( empty( $data ) ) {
		return esc_html__( 'No scheduled posts found.', 'reminder-log' );
	}

	ob_start();
	?>
	<div class="rlog-shortcode">
		<h3><?php esc_html_e( 'Scheduled Posts for Next 7 Days', 'reminder-log' ); ?></h3>
		<table class="rlog-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'reminder-log' ); ?></th>
					<th><?php esc_html_e( 'Post Title', 'reminder-log' ); ?></th>
					<th><?php esc_html_e( 'Scheduled Date', 'reminder-log' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $data as $log ) : ?>
					<?php foreach ( $log['posts'] as $post_id ) : ?>
						<?php $post = get_post( $post_id ); ?>
						<?php if ( $post ) : ?>
							<tr>
								<td><?php echo esc_html( $log['date'] ); ?></td>
								<td><?php echo esc_html( $post->post_title ); ?></td>
								<td><?php echo esc_html( mysql2date( 'Y-m-d', $post->post_date ) ); ?></td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'reminder_log', 'rlog_shortcode' );

/**
 * Activation hook.
 *
 * @return void
 */
function rlog_activate() {
	// No rewrite rules to flush.
}
register_activation_hook( __FILE__, 'rlog_activate' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function rlog_deactivate() {
	$timestamp = wp_next_scheduled( 'rl_daily_log' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'rl_daily_log' );
	}
}
register_deactivation_hook( __FILE__, 'rlog_deactivate' );
