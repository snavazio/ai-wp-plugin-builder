<?php
/**
 * Availability Check shortcode handler.
 *
 * @package Avch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [availability] shortcode.
 *
 * @return string HTML output.
 */
function avch_shortcode_availability() {
	ob_start();
	?>
	<div class="avch-availability-check">
		<form class="avch-availability-form">
			<label for="avch-date"><?php esc_html_e( 'Check date availability', 'ajax-availability-check' ); ?></label>
			<input type="date" id="avch-date" name="date" required>
			<button type="submit"><?php esc_html_e( 'Check', 'ajax-availability-check' ); ?></button>
			<div class="avch-availability-result"></div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'availability', 'avch_shortcode_availability' );
