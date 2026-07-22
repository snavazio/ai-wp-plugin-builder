<?php
/**
 * AJAX handlers for AJAX Load Comments functionality.
 *
 * @package Alcp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX load comments request.
 *
 * @return void
 */
function alcp_ajax_load_comments() {
	check_ajax_referer( 'alcp_load_comments_nonce', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	$page    = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;

	if ( $post_id < 1 ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'ajax-load-comments' ) ) );
	}

	if ( $page < 1 ) {
		$page = 1;
	}

	$args = array(
		'post_id' => $post_id,
		'status'  => 'approve',
		'number'  => 5,
		'offset'  => ( $page - 1 ) * 5,
		'orderby' => 'date',
		'order'   => 'DESC',
	);

	$comments = get_comments( $args );

	if ( empty( $comments ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'No more comments found.', 'ajax-load-comments' ) ) );
	}

	ob_start();
	foreach ( $comments as $comment ) {
		alcp_render_comment( $comment );
	}
	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'      => $html,
			'max_pages' => ceil( get_comments_number( $post_id ) / 5 ),
		)
	);
}
add_action( 'wp_ajax_alcp_load_comments', 'alcp_ajax_load_comments' );
add_action( 'wp_ajax_nopriv_alcp_load_comments', 'alcp_ajax_load_comments' );

/**
 * Render a single comment.
 *
 * @param WP_Comment $comment Comment object.
 * @return void
 */
function alcp_render_comment( $comment ) {
	?>
	<div class="alcp-comment" id="comment-<?php echo esc_attr( $comment->comment_ID ); ?>">
		<div class="alcp-comment-author">
			<?php echo esc_html( $comment->comment_author ); ?>
		</div>
		<div class="alcp-comment-content">
			<?php echo wp_kses_post( $comment->comment_content ); ?>
		</div>
	</div>
	<?php
}
