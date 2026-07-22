<?php
/**
 * Admin page for Announcement Bar plugin.
 *
 * @package Annbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Announcement Bar settings page.
 *
 * @return void
 */
function annbar_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'announcement-bar' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['annbar_save'] ) && isset( $_POST['annbar_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['annbar_nonce'] ) ), 'annbar_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'announcement-bar' ) );
		}

		// Sanitize input.
		$enabled = isset( $_POST['annbar_enabled'] ) ? ( '1' === sanitize_text_field( wp_unslash( $_POST['annbar_enabled'] ) ) ) : false;
		$message = '';
		if ( isset( $_POST['annbar_message'] ) ) {
			$message = wp_kses( wp_unslash( $_POST['annbar_message'] ), annbar_get_allowed_html() );
		}

		// Update option.
		update_option(
			'annbar_settings',
			array(
				'enabled' => $enabled,
				'message' => $message,
			)
		);
	}

	// Get current settings.
	$settings = get_option(
		'annbar_settings',
		array(
			'enabled' => true,
			'message' => '',
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Announcement Bar Settings', 'announcement-bar' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'annbar_save', 'annbar_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="annbar_enabled"><?php esc_html_e( 'Enable Announcement Bar', 'announcement-bar' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="annbar_enabled" id="annbar_enabled" value="1" <?php checked( $settings['enabled'], true ); ?> />
						<p class="description">
							<?php esc_html_e( 'Check to enable the announcement bar.', 'announcement-bar' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="annbar_message"><?php esc_html_e( 'Announcement Message', 'announcement-bar' ); ?></label>
					</th>
					<td>
						<textarea name="annbar_message" id="annbar_message" rows="5" class="large-text"><?php echo esc_textarea( $settings['message'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the announcement message (supports basic HTML: a, br, em, i, strong, b, p, ul, ol, li).', 'announcement-bar' ); ?>
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
 * Register the Announcement Bar settings page.
 *
 * @return void
 */
function annbar_register_admin_page() {
	add_options_page(
		__( 'Announcement Bar', 'announcement-bar' ),
		__( 'Announcement Bar', 'announcement-bar' ),
		'manage_options',
		'announcement-bar-settings',
		'annbar_admin_page_callback'
	);
}
add_action( 'admin_menu', 'annbar_register_admin_page' );

/**
 * Get allowed HTML tags and attributes for announcement message.
 *
 * @return array Allowed HTML tags and attributes.
 */
function annbar_get_allowed_html() {
	return array(
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
		),
		'br'     => array(),
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'p'      => array(),
		'ul'     => array(),
		'ol'     => array(),
		'li'     => array(),
	);
}
