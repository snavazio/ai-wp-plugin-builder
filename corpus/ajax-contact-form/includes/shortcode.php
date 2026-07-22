<?php
/**
 * Contact form shortcode handler.
 *
 * @package Acff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [contact_form] shortcode.
 *
 * @return string HTML output.
 */
function acff_contact_form_shortcode() {
	$nonce = wp_create_nonce( 'contact_form_nonce' );
	ob_start();
	?>
	<div class="acff-contact-form">
		<form id="acff-contact-form" method="post">
			<div class="acff-form-group">
				<label for="acff-name"><?php esc_html_e( 'Name', 'ajax-contact-form' ); ?></label>
				<input type="text" id="acff-name" name="name" required class="acff-input">
			</div>
			<div class="acff-form-group">
				<label for="acff-email"><?php esc_html_e( 'Email', 'ajax-contact-form' ); ?></label>
				<input type="email" id="acff-email" name="email" required class="acff-input">
			</div>
			<div class="acff-form-group">
				<label for="acff-message"><?php esc_html_e( 'Message', 'ajax-contact-form' ); ?></label>
				<textarea id="acff-message" name="message" required class="acff-textarea"></textarea>
			</div>
			<input type="hidden" name="action" value="contact_form_submit">
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
			<button type="submit" class="acff-submit-button"><?php esc_html_e( 'Submit', 'ajax-contact-form' ); ?></button>
			<div class="acff-form-response"></div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'contact_form', 'acff_contact_form_shortcode' );
