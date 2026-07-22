<?php
/**
 * AJAX Load Comments shortcode handler.
 *
 * @package Alcp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [load_comments] shortcode.
 *
 * @return string HTML output.
 */
function alcp_shortcode_load_comments() {
	$post_id = get_the_ID();

	if ( $post_id < 1 ) {
		return '<p>' . esc_html__( 'Cannot determine post ID.', 'ajax-load-comments' ) . '</p>';
	}

	$comment_count = get_comments_number( $post_id );

	if ( $comment_count < 1 ) {
		return '<p>' . esc_html__( 'No comments found.', 'ajax-load-comments' ) . '</p>';
	}

	ob_start();
	?>
	<div class="alcp-comments-container" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>" data-max-pages="<?php echo esc_attr( (string) ceil( $comment_count / 5 ) ); ?>">
		<div class="alcp-comments-list"></div>
		<?php if ( $comment_count > 5 ) : ?>
			<div class="alcp-load-comments-wrapper">
				<button class="alcp-load-comments-btn" data-page="1">
					<?php esc_html_e( 'Load More Comments', 'ajax-load-comments' ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'load_comments', 'alcp_shortcode_load_comments' );
