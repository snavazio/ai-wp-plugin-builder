<?php
/**
 * Admin page for Reading Progress plugin.
 *
 * @package Rpbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Reading Progress settings page.
 *
 * @return void
 */
function rpbar_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'reading-progress' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['rpbar_save'] ) && isset( $_POST['rpbar_nonce'] ) ) {
		// Verify nonce.
		$nonce = sanitize_text_field( wp_unslash( $_POST['rpbar_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'rpbar_settings' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'reading-progress' ) );
		}

		// Sanitize input.
		$enabled = isset( $_POST['rpbar_enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['rpbar_enabled'] ) );
		$color   = '#000000'; // Default.
		if ( isset( $_POST['rpbar_color'] ) ) {
			$color = sanitize_hex_color( wp_unslash( $_POST['rpbar_color'] ) );
			if ( ! $color ) {
				$color = '#000000';
			}
		}

		// Update options.
		update_option( 'rpbar_enabled', $enabled );
		update_option( 'rpbar_color', $color );
	}

	// Get current settings.
	$enabled = get_option( 'rpbar_enabled', false );
	$color   = get_option( 'rpbar_color', '#000000' );
	$color   = sanitize_hex_color( $color );
	if ( ! $color ) {
		$color = '#000000';
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Reading Progress', 'reading-progress' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'rpbar_settings', 'rpbar_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="rpbar_enabled">
							<?php esc_html_e( 'Enable Reading Progress', 'reading-progress' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="rpbar_enabled"
							id="rpbar_enabled"
							value="1"
							<?php checked( $enabled, true ); ?>
						/>
						<p class="description">
							<?php esc_html_e( 'When enabled, renders a reading progress bar on single posts.', 'reading-progress' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="rpbar_color">
							<?php esc_html_e( 'Bar Color', 'reading-progress' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							name="rpbar_color"
							id="rpbar_color"
							value="<?php echo esc_attr( $color ); ?>"
							class="rpbar-color-picker"
							placeholder="#000000"
						/>
						<p class="description">
							<?php esc_html_e( 'Enter a hex color code (e.g., #000000).', 'reading-progress' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
