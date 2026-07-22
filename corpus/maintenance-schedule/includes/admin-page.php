<?php
/**
 * Admin page for Maintenance Schedule plugin.
 *
 * @package Mstg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Maintenance Schedule settings page.
 *
 * @return void
 */
function mstg_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'maintenance-schedule' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['mstg_save'] ) && isset( $_POST['mstg_nonce'] ) ) {
		// Verify nonce.
		$nonce = sanitize_text_field( wp_unslash( $_POST['mstg_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'mstg_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'maintenance-schedule' ) );
		}

		// Sanitize input.
		$message = '';
		if ( isset( $_POST['mstg_message'] ) ) {
			$message = sanitize_text_field( wp_unslash( $_POST['mstg_message'] ) );
		}

		$enabled = false;
		if ( isset( $_POST['mstg_enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['mstg_enabled'] ) ) ) {
			$enabled = true;
		}

		// Update option.
		$settings = array(
			'message' => $message,
			'enabled' => $enabled,
		);
		update_option( 'mstg_settings', $settings );
	}

	// Get current settings.
	$settings = get_option(
		'mstg_settings',
		array(
			'enabled' => false,
			'message' => '',
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Maintenance Schedule', 'maintenance-schedule' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'mstg_settings_nonce', 'mstg_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="mstg_message"><?php esc_html_e( 'Maintenance Message', 'maintenance-schedule' ); ?></label>
					</th>
					<td>
						<textarea name="mstg_message" id="mstg_message" rows="5" class="regular-text"><?php echo esc_textarea( $settings['message'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the message to display to non-logged-in visitors when maintenance mode is active.', 'maintenance-schedule' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="mstg_enabled"><?php esc_html_e( 'Enable Maintenance Schedule', 'maintenance-schedule' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="mstg_enabled" id="mstg_enabled" value="1" <?php checked( $settings['enabled'], true ); ?> />
						<label for="mstg_enabled"><?php esc_html_e( 'Enable', 'maintenance-schedule' ); ?></label>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Register the Maintenance Schedule settings page.
 *
 * @return void
 */
function mstg_register_admin_page() {
	add_options_page(
		__( 'Maintenance Schedule', 'maintenance-schedule' ),
		__( 'Maintenance Schedule', 'maintenance-schedule' ),
		'manage_options',
		'maintenance-schedule',
		'mstg_admin_page_callback'
	);
}
add_action( 'admin_menu', 'mstg_register_admin_page' );
