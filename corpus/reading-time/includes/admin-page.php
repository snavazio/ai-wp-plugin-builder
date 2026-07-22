<?php
/**
 * Admin page for Reading Time plugin.
 *
 * @package Rtmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Reading Time settings page.
 *
 * @return void
 */
function rtmin_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'reading-time' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['rtmin_save'] ) && isset( $_POST['rtmin_nonce'] ) ) {
		// Verify nonce.
		$nonce = sanitize_text_field( wp_unslash( $_POST['rtmin_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'rtmin_settings' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'reading-time' ) );
		}

		// Sanitize input.
		$enabled = isset( $_POST['rtmin_enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['rtmin_enabled'] ) );

		// Update option.
		update_option( 'rtmin_enabled', $enabled );
	}

	// Get current settings.
	$enabled = get_option( 'rtmin_enabled', false );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Reading Time Settings', 'reading-time' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'rtmin_settings', 'rtmin_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="rtmin_enabled">
							<?php esc_html_e( 'Enable Reading Time Estimate', 'reading-time' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="rtmin_enabled"
							id="rtmin_enabled"
							value="1"
							<?php checked( $enabled, true ); ?>
						/>
						<p class="description">
							<?php esc_html_e( 'When enabled, displays a "X min read" estimate before single post content.', 'reading-time' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
