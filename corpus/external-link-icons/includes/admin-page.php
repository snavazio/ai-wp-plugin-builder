<?php
/**
 * Admin page for External Link Icons plugin.
 *
 * @package Elink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the External Link Icons settings page.
 *
 * @return void
 */
function elink_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'external-link-icons' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['elink_save'] ) && isset( $_POST['elink_nonce'] ) ) {
		// Verify nonce.
		$nonce = sanitize_text_field( wp_unslash( $_POST['elink_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'elink_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'external-link-icons' ) );
		}

		// Sanitize input.
		$enabled = isset( $_POST['elink_enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['elink_enabled'] ) );

		// Update option.
		update_option( 'elink_enabled', $enabled );
	}

	// Get current setting.
	$enabled = get_option( 'elink_enabled', false );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'External Link Icons', 'external-link-icons' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'elink_settings_nonce', 'elink_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="elink_enabled">
							<?php esc_html_e( 'Enable External Link Icons', 'external-link-icons' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="elink_enabled"
							id="elink_enabled"
							value="1"
							<?php checked( $enabled, true ); ?>
						/>
						<p class="description">
							<?php esc_html_e( 'When enabled, adds rel="noopener" and visual markers to external links in post content.', 'external-link-icons' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
