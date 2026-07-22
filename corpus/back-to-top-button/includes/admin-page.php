<?php
/**
 * Admin page for Back To Top Button plugin.
 *
 * @package Bttb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Back To Top Button settings page.
 *
 * @return void
 */
function bttb_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'back-to-top-button' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['bttb_save'] ) && isset( $_POST['bttb_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bttb_nonce'] ) ), 'bttb_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'back-to-top-button' ) );
		}

		// Sanitize input.
		$enabled     = isset( $_POST['bttb_enabled'] ) ? ( '1' === sanitize_text_field( wp_unslash( $_POST['bttb_enabled'] ) ) ) : false;
		$button_text = '';
		if ( isset( $_POST['bttb_button_text'] ) ) {
			$button_text = sanitize_text_field( wp_unslash( $_POST['bttb_button_text'] ) );
		}

		// Update option.
		update_option(
			'bttb_settings',
			array(
				'enabled'     => $enabled,
				'button_text' => $button_text,
			)
		);
	}

	// Get current settings.
	$settings = get_option(
		'bttb_settings',
		array(
			'enabled'     => true,
			'button_text' => __( 'Back to Top', 'back-to-top-button' ),
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Back To Top Button Settings', 'back-to-top-button' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'bttb_save', 'bttb_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="bttb_enabled"><?php esc_html_e( 'Enable Button', 'back-to-top-button' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="bttb_enabled" id="bttb_enabled" value="1" <?php checked( $settings['enabled'], true ); ?> />
						<p class="description">
							<?php esc_html_e( 'Check to enable the back-to-top button.', 'back-to-top-button' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="bttb_button_text"><?php esc_html_e( 'Button Text', 'back-to-top-button' ); ?></label>
					</th>
					<td>
						<input type="text" name="bttb_button_text" id="bttb_button_text" value="<?php echo esc_attr( $settings['button_text'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the text to display on the back-to-top button.', 'back-to-top-button' ); ?>
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
 * Register the Back To Top Button settings page.
 *
 * @return void
 */
function bttb_register_admin_page() {
	add_options_page(
		__( 'Back To Top Button', 'back-to-top-button' ),
		__( 'Back To Top Button', 'back-to-top-button' ),
		'manage_options',
		'bttb-settings',
		'bttb_admin_page_callback'
	);
}
add_action( 'admin_menu', 'bttb_register_admin_page' );
