<?php
/**
 * Admin page for Custom Footer Text plugin.
 *
 * @package Cftx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Custom Footer Text settings page.
 *
 * @return void
 */
function cftx_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'custom-footer-text' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['cftx_save'] ) && isset( $_POST['cftx_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cftx_nonce'] ) ), 'cftx_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'custom-footer-text' ) );
		}

		// Sanitize input.
		$footer_text = '';
		if ( isset( $_POST['cftx_footer_text'] ) ) {
			$footer_text = wp_kses( wp_unslash( $_POST['cftx_footer_text'] ), cftx_get_allowed_html() );
		}

		// Update option.
		update_option( 'cftx_footer_text', $footer_text );
	}

	// Get current footer text.
	$footer_text = get_option( 'cftx_footer_text', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Custom Footer Text', 'custom-footer-text' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'cftx_save', 'cftx_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="cftx_footer_text"><?php esc_html_e( 'Footer Text', 'custom-footer-text' ); ?></label>
					</th>
					<td>
						<textarea name="cftx_footer_text" id="cftx_footer_text" rows="5" class="large-text"><?php echo esc_textarea( $footer_text ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter footer text with basic HTML support (allowed tags: a, br, em, i, strong, b, p, ul, ol, li, h1, h2, h3, h4, h5, h6, blockquote, code, pre).', 'custom-footer-text' ); ?>
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
 * Register the Custom Footer Text settings page.
 *
 * @return void
 */
function cftx_register_admin_page() {
	add_options_page(
		__( 'Custom Footer Text', 'custom-footer-text' ),
		__( 'Custom Footer Text', 'custom-footer-text' ),
		'manage_options',
		'custom-footer-text',
		'cftx_admin_page_callback'
	);
}
add_action( 'admin_menu', 'cftx_register_admin_page' );

/**
 * Get allowed HTML tags and attributes for footer text.
 *
 * @return array Allowed HTML tags and attributes.
 */
function cftx_get_allowed_html() {
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
