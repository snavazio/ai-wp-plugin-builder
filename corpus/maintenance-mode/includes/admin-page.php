<?php
/**
 * Admin page for Maintenance Mode plugin.
 *
 * @package Mmtm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Maintenance Mode settings page.
 *
 * @return void
 */
function mmtm_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'maintenance-mode' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['mmtm_save'] ) && isset( $_POST['mmtm_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mmtm_nonce'] ) ), 'mmtm_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'maintenance-mode' ) );
		}

		// Sanitize input.
		$message = '';
		if ( isset( $_POST['mmtm_message'] ) ) {
			$message = sanitize_text_field( wp_unslash( $_POST['mmtm_message'] ) );
		}

		$enabled = false;
		if ( isset( $_POST['mmtm_enabled'] ) && '1' === $_POST['mmtm_enabled'] ) {
			$enabled = true;
		}

		// Update option.
		$settings = array(
			'message' => $message,
			'enabled' => $enabled,
		);
		update_option( 'mmtm_settings', $settings );
	}

	// Get current settings.
	$settings = get_option(
		'mmtm_settings',
		array(
			'enabled' => false,
			'message' => '',
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Maintenance Mode', 'maintenance-mode' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'mmtm_settings_nonce', 'mmtm_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="mmtm_message"><?php esc_html_e( 'Maintenance Message', 'maintenance-mode' ); ?></label>
					</th>
					<td>
						<textarea name="mmtm_message" id="mmtm_message" rows="5" class="regular-text"><?php echo esc_textarea( $settings['message'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the message to display to non-logged-in visitors when maintenance mode is active.', 'maintenance-mode' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="mmtm_enabled"><?php esc_html_e( 'Enable Maintenance Mode', 'maintenance-mode' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="mmtm_enabled" id="mmtm_enabled" value="1" <?php checked( $settings['enabled'], true ); ?> />
						<label for="mmtm_enabled"><?php esc_html_e( 'Enable', 'maintenance-mode' ); ?></label>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Register the Maintenance Mode settings page.
 *
 * @return void
 */
function mmtm_register_admin_page() {
	add_options_page(
		__( 'Maintenance Mode', 'maintenance-mode' ),
		__( 'Maintenance Mode', 'maintenance-mode' ),
		'manage_options',
		'maintenance-mode',
		'mmtm_admin_page_callback'
	);
}
add_action( 'admin_menu', 'mmtm_register_admin_page' );
