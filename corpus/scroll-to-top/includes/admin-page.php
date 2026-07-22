<?php
/**
 * Admin page for Scroll To Top plugin.
 *
 * @package Sttop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Scroll To Top settings page.
 *
 * @return void
 */
function sttop_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'scroll-to-top' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['sttop_save'] ) && isset( $_POST['sttop_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sttop_nonce'] ) ), 'sttop_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'scroll-to-top' ) );
		}

		// Sanitize input.
		$button_text = '';
		if ( isset( $_POST['sttop_button_text'] ) ) {
			$button_text = sanitize_text_field( wp_unslash( $_POST['sttop_button_text'] ) );
		}

		$button_color = '';
		if ( isset( $_POST['sttop_button_color'] ) ) {
			$button_color = sanitize_text_field( wp_unslash( $_POST['sttop_button_color'] ) );
		}

		// Update option.
		update_option(
			'sttop_settings',
			array(
				'button_text'  => $button_text,
				'button_color' => $button_color,
			)
		);
	}

	// Get current settings.
	$settings = get_option(
		'sttop_settings',
		array(
			'button_text'  => __( 'Scroll to Top', 'scroll-to-top' ),
			'button_color' => '#000000',
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Scroll To Top Settings', 'scroll-to-top' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'sttop_save', 'sttop_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="sttop_button_text"><?php esc_html_e( 'Button Text', 'scroll-to-top' ); ?></label>
					</th>
					<td>
						<input type="text" name="sttop_button_text" id="sttop_button_text" value="<?php echo esc_attr( $settings['button_text'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the text to display on the scroll to top button.', 'scroll-to-top' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sttop_button_color"><?php esc_html_e( 'Button Color', 'scroll-to-top' ); ?></label>
					</th>
					<td>
						<input type="text" name="sttop_button_color" id="sttop_button_color" value="<?php echo esc_attr( $settings['button_color'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter a hex color code (e.g., #000000 for black).', 'scroll-to-top' ); ?>
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
 * Register the Scroll To Top settings page.
 *
 * @return void
 */
function sttop_register_admin_page() {
	add_options_page(
		__( 'Scroll To Top', 'scroll-to-top' ),
		__( 'Scroll To Top', 'scroll-to-top' ),
		'manage_options',
		'scroll-to-top-settings',
		'sttop_admin_page_callback'
	);
}
add_action( 'admin_menu', 'sttop_register_admin_page' );
