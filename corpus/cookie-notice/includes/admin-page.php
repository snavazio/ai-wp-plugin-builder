<?php
/**
 * Admin page for Cookie Notice plugin.
 *
 * @package Cns1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Cookie Notice settings page.
 *
 * @return void
 */
function cns1_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cookie-notice' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['cns1_save'] ) && isset( $_POST['cns1_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cns1_nonce'] ) ), 'cns1_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'cookie-notice' ) );
		}

		// Sanitize input.
		$message = '';
		if ( isset( $_POST['cns1_message'] ) ) {
			$message = wp_kses_post( wp_unslash( $_POST['cns1_message'] ) );
		}

		$button_label = '';
		if ( isset( $_POST['cns1_button_label'] ) ) {
			$button_label = sanitize_text_field( wp_unslash( $_POST['cns1_button_label'] ) );
		}

		// Update option.
		update_option(
			'cns1_settings',
			array(
				'message'      => $message,
				'button_label' => $button_label,
			)
		);
	}

	// Get current settings.
	$settings = get_option(
		'cns1_settings',
		array(
			'message'      => __( 'This website uses cookies to improve your experience. By continuing to use this site, you agree to our use of cookies.', 'cookie-notice' ),
			'button_label' => __( 'Accept', 'cookie-notice' ),
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Cookie Notice Settings', 'cookie-notice' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'cns1_save', 'cns1_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="cns1_message"><?php esc_html_e( 'Cookie Message', 'cookie-notice' ); ?></label>
					</th>
					<td>
						<textarea name="cns1_message" id="cns1_message" rows="5" class="large-text"><?php echo esc_textarea( $settings['message'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the message to display in the cookie consent banner (supports basic HTML: a, br, em, i, strong, b, p).', 'cookie-notice' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="cns1_button_label"><?php esc_html_e( 'Button Label', 'cookie-notice' ); ?></label>
					</th>
					<td>
						<input type="text" name="cns1_button_label" id="cns1_button_label" value="<?php echo esc_attr( $settings['button_label'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the text for the cookie consent button.', 'cookie-notice' ); ?>
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
 * Register the Cookie Notice settings page.
 *
 * @return void
 */
function cns1_register_admin_page() {
	add_options_page(
		__( 'Cookie Notice', 'cookie-notice' ),
		__( 'Cookie Notice', 'cookie-notice' ),
		'manage_options',
		'cookie-notice-settings',
		'cns1_admin_page_callback'
	);
}
add_action( 'admin_menu', 'cns1_register_admin_page' );
