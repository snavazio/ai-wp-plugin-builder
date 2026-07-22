<?php
/**
 * Admin page for Favicon Manager plugin.
 *
 * @package Fvman
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Favicon Manager settings page.
 *
 * @return void
 */
function fvman_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'favicon-manager' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['fvman_save'] ) && isset( $_POST['fvman_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fvman_nonce'] ) ), 'fvman_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'favicon-manager' ) );
		}

		// Sanitize input.
		$url = '';
		if ( isset( $_POST['fvman_favicon_url'] ) ) {
			$url = esc_url_raw( wp_unslash( $_POST['fvman_favicon_url'] ) );
		}

		// Update option.
		update_option( 'fvman_favicon_url', $url );
	}

	// Get current URL.
	$url = get_option( 'fvman_favicon_url', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Favicon Manager', 'favicon-manager' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'fvman_save', 'fvman_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="fvman_favicon_url"><?php esc_html_e( 'Favicon URL', 'favicon-manager' ); ?></label>
					</th>
					<td>
						<input type="url" name="fvman_favicon_url" id="fvman_favicon_url" value="<?php echo esc_attr( $url ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the full URL to your favicon (e.g., https://example.com/favicon.ico).', 'favicon-manager' ); ?>
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
 * Register the Favicon Manager settings page.
 *
 * @return void
 */
function fvman_add_settings_page() {
	add_options_page(
		__( 'Favicon Manager', 'favicon-manager' ),
		__( 'Favicon Manager', 'favicon-manager' ),
		'manage_options',
		'favicon-manager-settings',
		'fvman_admin_page_callback'
	);
}
add_action( 'admin_menu', 'fvman_add_settings_page' );
