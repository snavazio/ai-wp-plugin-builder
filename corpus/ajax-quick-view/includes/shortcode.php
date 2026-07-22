<?php
/**
 * AJAX Quick View shortcode handler.
 *
 * @package Aqv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [quick_view] shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function aqv1_shortcode_quick_view( $atts ) {
	$atts = shortcode_atts(
		array(
			'post_id' => '',
		),
		$atts,
		'quick_view'
	);

	$post_id = absint( $atts['post_id'] );

	if ( ! $post_id ) {
		return '<p>' . esc_html__( 'Please specify a valid post ID.', 'ajax-quick-view' ) . '</p>';
	}

	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return '<p>' . esc_html__( 'The requested post is not published.', 'ajax-quick-view' ) . '</p>';
	}

	ob_start();
	?>
	<div class="aqv1-quick-view-container" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
		<button class="aqv1-quick-view-button" aria-label="<?php esc_attr_e( 'View post', 'ajax-quick-view' ); ?>">
			<?php esc_html_e( 'Quick View', 'ajax-quick-view' ); ?>
		</button>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'quick_view', 'aqv1_shortcode_quick_view' );
