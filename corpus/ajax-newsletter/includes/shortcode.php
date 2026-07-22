<?php
/**
 * AJAX Newsletter shortcode handler.
 *
 * @package Anews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [newsletter] shortcode.
 *
 * @return string HTML output.
 */
function anews_shortcode_newsletter() {
	ob_start();
	?>
	<div class="anews-newsletter-form">
		<form id="anews-subscribe-form">
			<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'ajax-newsletter' ); ?>" required />
			<button type="submit"><?php esc_html_e( 'Subscribe', 'ajax-newsletter' ); ?></button>
			<div class="anews-response"></div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'newsletter', 'anews_shortcode_newsletter' );
