<?php
/**
 * Event RSVP shortcode handler.
 *
 * @package Ersv
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [event_rsvp] shortcode.
 *
 * @return string HTML output.
 */
function ersv_event_rsvp_shortcode() {
	if ( ! is_singular( 'event' ) ) {
		return '<p>' . esc_html__( 'This form must be used on an event page.', 'event-rsvp' ) . '</p>';
	}

	$post_id = get_the_ID();
	$nonce   = wp_create_nonce( 'ersv_rsvp_nonce' );
	ob_start();
	?>
	<div class="ersv-rsvp-form">
		<form id="ersv-rsvp-form" method="post">
			<input type="hidden" name="event_id" value="<?php echo esc_attr( (string) $post_id ); ?>">
			<input type="hidden" name="action" value="ersv_rsvp">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">
			<button type="submit" class="ersv-rsvp-submit">
				<?php esc_html_e( 'RSVP', 'event-rsvp' ); ?>
			</button>
			<div class="ersv-rsvp-response"></div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'event_rsvp', 'ersv_event_rsvp_shortcode' );
