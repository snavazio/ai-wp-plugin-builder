<?php
/**
 * Admin page for Login Logo plugin.
 *
 * @package Lgnl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Login Logo settings page.
 *
 * @return void
 */
function lgnl_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'login-logo' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['lgnl_save'] ) && isset( $_POST['lgnl_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lgnl_nonce'] ) ), 'lgnl_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'login-logo' ) );
		}

		// Sanitize input.
		$logo_url = '';
		if ( isset( $_POST['lgnl_logo_url'] ) ) {
			$logo_url = esc_url_raw( wp_unslash( $_POST['lgnl_logo_url'] ) );
		}

		// Update option.
		update_option( 'lgnl_logo_url', $logo_url );
	}

	// Get current logo URL.
	$logo_url = get_option( 'lgnl_logo_url', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Login Logo', 'login-logo' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'lgnl_settings_nonce', 'lgnl_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="lgnl_logo_url"><?php esc_html_e( 'Custom Logo URL', 'login-logo' ); ?></label>
					</th>
					<td>
						<input type="url" name="lgnl_logo_url" id="lgnl_logo_url" value="<?php echo esc_url( $logo_url ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the full URL of your custom logo image (e.g., https://example.com/logo.png).', 'login-logo' ); ?>
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
 * Register the Login Logo settings page.
 *
 * @return void
 */
function lgnl_register_admin_page() {
	add_options_page(
		__( 'Login Logo', 'login-logo' ),
		__( 'Login Logo', 'login-logo' ),
		'manage_options',
		'lgnl-settings',
		'lgnl_admin_page_callback'
	);
}
add_action( 'admin_menu', 'lgnl_register_admin_page' );
