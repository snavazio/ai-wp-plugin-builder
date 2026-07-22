<?php
/**
 * Admin page for Custom 404 Message plugin.
 *
 * @package C404m
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Custom 404 Message settings page.
 *
 * @return void
 */
function c404m_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'custom-404-message' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['c404m_save'] ) && isset( $_POST['c404m_nonce'] ) ) {
		// Verify nonce.
		$nonce = sanitize_text_field( wp_unslash( $_POST['c404m_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'c404m_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'custom-404-message' ) );
		}

		// Sanitize input (allow basic HTML via wp_kses_post).
		$message = '';
		if ( isset( $_POST['c404m_message'] ) ) {
			$message = wp_kses_post( wp_unslash( $_POST['c404m_message'] ) );
		}

		// Update option.
		update_option( 'c404m_message', $message );
	}

	// Get current settings.
	$message = get_option( 'c404m_message', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Custom 404 Message', 'custom-404-message' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'c404m_settings_nonce', 'c404m_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="c404m_message"><?php esc_html_e( 'Custom 404 Message', 'custom-404-message' ); ?></label>
					</th>
					<td>
						<textarea name="c404m_message" id="c404m_message" rows="5" class="regular-text"><?php echo esc_textarea( $message ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the message to display on 404 pages (basic HTML allowed).', 'custom-404-message' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Register the Custom 404 Message settings page.
 *
 * @return void
 */
function c404m_add_404_settings() {
	add_options_page(
		__( 'Custom 404 Message', 'custom-404-message' ),
		__( '404 Message', 'custom-404-message' ),
		'manage_options',
		'c404m-404-settings',
		'c404m_admin_page_callback'
	);
}
add_action( 'admin_menu', 'c404m_add_404_settings' );
