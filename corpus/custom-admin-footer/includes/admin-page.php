<?php
/**
 * Admin page for Custom Admin Footer plugin.
 *
 * @package Caf1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Custom Admin Footer settings page.
 *
 * @return void
 */
function caf1_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'custom-admin-footer' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['caf1_save'] ) && isset( $_POST['caf1_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['caf1_nonce'] ) ), 'caf1_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'custom-admin-footer' ) );
		}

		// Sanitize input.
		$footer_text = '';
		if ( isset( $_POST['caf1_footer_text'] ) ) {
			$footer_text = wp_kses( wp_unslash( $_POST['caf1_footer_text'] ), caf1_get_allowed_html() );
		}

		// Update option.
		update_option( 'caf1_footer_text', $footer_text );
	}

	// Get current footer text.
	$footer_text = get_option( 'caf1_footer_text', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Custom Admin Footer', 'custom-admin-footer' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'caf1_save', 'caf1_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="caf1_footer_text"><?php esc_html_e( 'Footer Text', 'custom-admin-footer' ); ?></label>
					</th>
					<td>
						<textarea name="caf1_footer_text" id="caf1_footer_text" rows="5" class="large-text"><?php echo esc_textarea( $footer_text ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter footer text with basic HTML support (allowed tags: a, br, em, i, strong, b, p, ul, ol, li, h1, h2, h3, h4, h5, h6, blockquote, code, pre).', 'custom-admin-footer' ); ?>
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
 * Register the Custom Admin Footer settings page.
 *
 * @return void
 */
function caf1_register_admin_page() {
	add_options_page(
		__( 'Custom Admin Footer', 'custom-admin-footer' ),
		__( 'Custom Admin Footer', 'custom-admin-footer' ),
		'manage_options',
		'custom-admin-footer',
		'caf1_admin_page_callback'
	);
}
add_action( 'admin_menu', 'caf1_register_admin_page' );

/**
 * Get allowed HTML tags and attributes for footer text.
 *
 * @return array Allowed HTML tags and attributes.
 */
function caf1_get_allowed_html() {
	return array(
		'a'          => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
		),
		'br'         => array(),
		'em'         => array(),
		'i'          => array(),
		'strong'     => array(),
		'b'          => array(),
		'p'          => array(),
		'ul'         => array(),
		'ol'         => array(),
		'li'         => array(),
		'h1'         => array(),
		'h2'         => array(),
		'h3'         => array(),
		'h4'         => array(),
		'h5'         => array(),
		'h6'         => array(),
		'blockquote' => array(),
		'code'       => array(),
		'pre'        => array(),
	);
}
