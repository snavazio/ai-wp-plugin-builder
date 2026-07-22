<?php
/**
 * Add Press Release meta box for release date.
 *
 * @package Prcs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the press release meta box to the press release editor.
 *
 * @return void
 */
function prcs_add_press_release_meta_box() {
	add_meta_box(
		'prcs_press_release_meta_box',
		__( 'Release Date', 'press-releases' ),
		'prcs_render_press_release_meta_box',
		'press_release',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_press_release', 'prcs_add_press_release_meta_box' );

/**
 * Render the press release meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function prcs_render_press_release_meta_box( $post ) {
	$release_date = get_post_meta( $post->ID, 'prcs_release_date', true );

	// Nonce for security.
	wp_nonce_field( 'prcs_save_press_release_meta', 'prcs_press_release_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="prcs_release_date"><?php esc_html_e( 'Release Date', 'press-releases' ); ?></label>
				</th>
				<td>
					<input
						type="date"
						id="prcs_release_date"
						name="prcs_release_date"
						value="<?php echo esc_attr( $release_date ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Format: YYYY-MM-DD', 'press-releases' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save press release meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function prcs_save_press_release_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['prcs_press_release_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['prcs_press_release_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['prcs_press_release_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'prcs_save_press_release_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$release_date = isset( $_POST['prcs_release_date'] ) ? sanitize_text_field( wp_unslash( $_POST['prcs_release_date'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'prcs_release_date', $release_date );
}
add_action( 'save_post_press_release', 'prcs_save_press_release_meta', 10, 2 );
