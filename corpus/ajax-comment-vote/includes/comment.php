<?php
/**
 * Add vote button to comment text.
 *
 * @package Acv1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add upvote button to comment text.
 *
 * @param string $comment_text Comment text.
 * @param object $comment      Comment object.
 * @return string Modified comment text.
 */
function acv1_add_vote_button( $comment_text, $comment ) {
	$vote_count = (int) get_comment_meta( $comment->comment_ID, '_acv1_vote_count', true );
	$nonce      = wp_create_nonce( 'acv_vote_nonce' );

	ob_start();
	?>
	<div class="acv1-comment-vote" data-comment-id="<?php echo esc_attr( (string) $comment->comment_ID ); ?>">
		<button class="acv1-vote-button" type="button" aria-label="<?php esc_attr_e( 'Upvote comment', 'ajax-comment-vote' ); ?>">
			<?php echo esc_html( (string) $vote_count ); ?>
		</button>
	</div>
	<?php
	return $comment_text . ob_get_clean();
}
add_filter( 'comment_text', 'acv1_add_vote_button', 10, 2 );
