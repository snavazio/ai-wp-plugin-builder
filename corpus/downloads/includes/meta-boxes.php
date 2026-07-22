<?php
/**
 * Add Download meta box for file URL and download count.
 *
 * @package Dlm1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the download meta box to the download editor.
 *
 * @return void
 */
function dlm1_add_download_meta_box() {
	add_meta_box(
		'dlm1_download_meta_box',
		__( 'Download Details', 'downloads' ),
		'dlm1_render_download_meta_box',
		'download',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_download', 'dlm1_add_download_meta_box' );

/**
 * Render the download meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function dlm1_render_download_meta_box( $post ) {
	$file_url = get_post_meta( $post->ID, 'dlm1_file_url', true );
	$count    = get_post_meta( $post->ID, 'dlm1_download_count', true );

	// Nonce for security.
	wp_nonce_field( 'dlm1_save_download_meta', 'dlm1_download_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="dlm1_file_url"><?php esc_html_e( 'File URL', 'downloads' ); ?></label>
				</th>
				<td>
					<input
						type="url"
						id="dlm1_file_url"
						name="dlm1_file_url"
						value="<?php echo esc_attr( $file_url ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Full URL to the downloadable file', 'downloads' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="dlm1_download_count"><?php esc_html_e( 'Download Count', 'downloads' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="dlm1_download_count"
						name="dlm1_download_count"
						value="<?php echo esc_attr( $count ); ?>"
						min="0"
						class="small-text"
					/>
					<p class="description"><?php esc_html_e( 'Number of times this download has been accessed', 'downloads' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save download meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function dlm1_save_download_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['dlm1_download_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['dlm1_download_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['dlm1_download_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'dlm1_save_download_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$file_url = isset( $_POST['dlm1_file_url'] ) ? esc_url_raw( wp_unslash( $_POST['dlm1_file_url'] ) ) : '';
	$count    = isset( $_POST['dlm1_download_count'] ) ? absint( wp_unslash( $_POST['dlm1_download_count'] ) ) : 0;

	// Update meta.
	update_post_meta( $post_id, 'dlm1_file_url', $file_url );
	update_post_meta( $post_id, 'dlm1_download_count', $count );
}
add_action( 'save_post_download', 'dlm1_save_download_meta', 10, 2 );
