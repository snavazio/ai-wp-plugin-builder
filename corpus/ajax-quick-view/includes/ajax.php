<?php
/**
 * AJAX handlers for AJAX Quick View functionality.
 *
 * @package Aqv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle AJAX quick view request.
 *
 * @return void
 */
function aqv1_ajax_quick_view() {
	check_ajax_referer( 'quick_view_nonce', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;

	if ( ! $post_id || ! get_post( $post_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'ajax-quick-view' ) ) );
	}

	$post = get_post( $post_id );
	if ( 'publish' !== $post->post_status ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Post is not published.', 'ajax-quick-view' ) ) );
	}

	ob_start();
	?>
	<div class="aqv1-modal-content">
		<h2 class="aqv1-modal-title"><?php echo esc_html( $post->post_title ); ?></h2>
		<div class="aqv1-modal-excerpt"><?php echo wp_kses_post( get_the_excerpt( $post_id ) ); ?></div>
		<div class="aqv1-modal-body"><?php echo wp_kses_post( $post->post_content ); ?></div>
	</div>
	<?php
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_ajax_quick_view', 'aqv1_ajax_quick_view' );
add_action( 'wp_ajax_nopriv_ajax_quick_view', 'aqv1_ajax_quick_view' );
