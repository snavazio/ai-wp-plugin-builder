<?php
/**
 * Settings page, save handler and clear-data handler.
 *
 * @package Hmtrk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the settings page.
 *
 * @return void
 */
function hmtrk_register_menu() {
	add_options_page(
		__( 'Heatmap Tracker', 'heatmap-tracker' ),
		__( 'Heatmap Tracker', 'heatmap-tracker' ),
		'manage_options',
		'hmtrk-settings',
		'hmtrk_render_settings'
	);
}
add_action( 'admin_menu', 'hmtrk_register_menu' );

/**
 * Render the settings page.
 *
 * @return void
 */
function hmtrk_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tracked = hmtrk_get_tracked_ids();
	$posts   = get_posts(
		array(
			'post_type'              => array( 'page', 'post' ),
			'post_status'            => 'publish',
			'posts_per_page'         => 100, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	// Read-only notice flag; sanitized and compared to an allow-list.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$notice = isset( $_GET['hmtrk_notice'] ) ? sanitize_key( wp_unslash( $_GET['hmtrk_notice'] ) ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Heatmap Tracker', 'heatmap-tracker' ); ?></h1>
		<?php if ( 'saved' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'heatmap-tracker' ); ?></p></div>
		<?php elseif ( 'cleared' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Heatmap data cleared.', 'heatmap-tracker' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="hmtrk_save_settings" />
			<?php wp_nonce_field( 'hmtrk_save_settings', 'hmtrk_nonce' ); ?>

			<h2><?php esc_html_e( 'Tracked pages and posts', 'heatmap-tracker' ); ?></h2>
			<p><?php esc_html_e( 'Only ticked pages and posts are tracked. Administrators are never tracked.', 'heatmap-tracker' ); ?></p>
			<fieldset>
				<?php foreach ( $posts as $item ) : ?>
					<label style="display:block;margin-bottom:4px;">
						<input type="checkbox" name="hmtrk_tracked_posts[]" value="<?php echo esc_attr( (string) $item->ID ); ?>" <?php checked( in_array( (int) $item->ID, $tracked, true ) ); ?> />
						<?php echo esc_html( get_the_title( $item ) ); ?>
						<em>(<?php echo esc_html( $item->post_type ); ?>)</em>
					</label>
				<?php endforeach; ?>
				<?php if ( empty( $posts ) ) : ?>
					<p><?php esc_html_e( 'No published pages or posts found.', 'heatmap-tracker' ); ?></p>
				<?php endif; ?>
			</fieldset>

			<h2><?php esc_html_e( 'Uninstall', 'heatmap-tracker' ); ?></h2>
			<label>
				<input type="checkbox" name="hmtrk_delete_on_uninstall" value="1" <?php checked( (bool) get_option( 'hmtrk_delete_on_uninstall', false ) ); ?> />
				<?php esc_html_e( 'Delete all heatmap data when the plugin is deleted', 'heatmap-tracker' ); ?>
			</label>

			<?php submit_button( __( 'Save settings', 'heatmap-tracker' ) ); ?>
		</form>

		<hr />
		<h2><?php esc_html_e( 'Clear heatmap data', 'heatmap-tracker' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="hmtrk_clear_data" />
			<?php wp_nonce_field( 'hmtrk_clear_data', 'hmtrk_nonce' ); ?>
			<p><?php esc_html_e( 'Permanently deletes every recorded click and scroll event.', 'heatmap-tracker' ); ?></p>
			<?php submit_button( __( 'Clear heatmap data', 'heatmap-tracker' ), 'delete' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Handle the settings save.
 *
 * @return void
 */
function hmtrk_handle_save_settings() {
	check_admin_referer( 'hmtrk_save_settings', 'hmtrk_nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'heatmap-tracker' ), '', array( 'response' => 403 ) );
	}

	$raw = isset( $_POST['hmtrk_tracked_posts'] ) && is_array( $_POST['hmtrk_tracked_posts'] )
		? array_map( 'absint', wp_unslash( $_POST['hmtrk_tracked_posts'] ) )
		: array();

	$ids = array();
	foreach ( array_unique( $raw ) as $id ) {
		$type = $id ? get_post_type( $id ) : false;
		if ( in_array( $type, array( 'page', 'post' ), true ) && 'publish' === get_post_status( $id ) ) {
			$ids[] = $id;
		}
	}

	update_option( 'hmtrk_tracked_posts', $ids );
	update_option( 'hmtrk_delete_on_uninstall', empty( $_POST['hmtrk_delete_on_uninstall'] ) ? 0 : 1 );

	wp_safe_redirect( add_query_arg( 'hmtrk_notice', 'saved', admin_url( 'options-general.php?page=hmtrk-settings' ) ) );
	exit;
}
add_action( 'admin_post_hmtrk_save_settings', 'hmtrk_handle_save_settings' );

/**
 * Handle the clear-data button.
 *
 * @return void
 */
function hmtrk_handle_clear_data() {
	check_admin_referer( 'hmtrk_clear_data', 'hmtrk_nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'heatmap-tracker' ), '', array( 'response' => 403 ) );
	}

	global $wpdb;
	$table = hmtrk_table();
	$wpdb->query( "DELETE FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	wp_safe_redirect( add_query_arg( 'hmtrk_notice', 'cleared', admin_url( 'options-general.php?page=hmtrk-settings' ) ) );
	exit;
}
add_action( 'admin_post_hmtrk_clear_data', 'hmtrk_handle_clear_data' );
