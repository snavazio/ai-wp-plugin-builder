<?php
/**
 * Add Speaker meta box for Twitter handle.
 *
 * @package Spkrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the speaker meta box to the speaker editor.
 *
 * @return void
 */
function spkrs_add_speaker_meta_box() {
	add_meta_box(
		'spkrs_speaker_meta_box',
		__( 'Speaker Twitter', 'speakers' ),
		'spkrs_render_speaker_meta_box',
		'speaker',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_speaker', 'spkrs_add_speaker_meta_box' );

/**
 * Render the speaker meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function spkrs_render_speaker_meta_box( $post ) {
	$twitter_handle = get_post_meta( $post->ID, 'spkrs_twitter_handle', true );

	// Nonce for security.
	wp_nonce_field( 'spkrs_save_speaker_meta', 'spkrs_speaker_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="spkrs_twitter_handle"><?php esc_html_e( 'Twitter Handle', 'speakers' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="spkrs_twitter_handle"
						name="spkrs_twitter_handle"
						value="<?php echo esc_attr( $twitter_handle ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "@johndoe", "johndoe"', 'speakers' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save speaker meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function spkrs_save_speaker_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['spkrs_speaker_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['spkrs_speaker_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['spkrs_speaker_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'spkrs_save_speaker_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$twitter_handle = isset( $_POST['spkrs_twitter_handle'] ) ? sanitize_text_field( wp_unslash( $_POST['spkrs_twitter_handle'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'spkrs_twitter_handle', $twitter_handle );
}
add_action( 'save_post_speaker', 'spkrs_save_speaker_meta', 10, 2 );
