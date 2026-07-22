<?php
/**
 * Add Case Study meta boxes for client and outcome.
 *
 * @package Cstuds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the case study meta box to the editor.
 *
 * @return void
 */
function cstuds_add_meta_boxes() {
	add_meta_box(
		'cstuds_case_study_meta_box',
		__( 'Case Study Details', 'case-studies' ),
		'cstuds_render_meta_box',
		'case-study',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_case-study', 'cstuds_add_meta_boxes' );

/**
 * Render the case study meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function cstuds_render_meta_box( $post ) {
	$client  = get_post_meta( $post->ID, 'cstuds_client', true );
	$outcome = get_post_meta( $post->ID, 'cstuds_outcome', true );

	// Nonce for security.
	wp_nonce_field( 'cstuds_save_case_study_meta', 'cstuds_case_study_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="cstuds_client"><?php esc_html_e( 'Client', 'case-studies' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="cstuds_client"
						name="cstuds_client"
						value="<?php echo esc_attr( $client ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Name of the client', 'case-studies' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cstuds_outcome"><?php esc_html_e( 'Outcome', 'case-studies' ); ?></label>
				</th>
				<td>
					<textarea
						id="cstuds_outcome"
						name="cstuds_outcome"
						rows="5"
						class="large-text"
					><?php echo esc_textarea( $outcome ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Description of the outcome', 'case-studies' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save case study meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function cstuds_save_meta_box_data( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['cstuds_case_study_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cstuds_case_study_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['cstuds_case_study_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'cstuds_save_case_study_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$client  = isset( $_POST['cstuds_client'] ) ? sanitize_text_field( wp_unslash( $_POST['cstuds_client'] ) ) : '';
	$outcome = isset( $_POST['cstuds_outcome'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cstuds_outcome'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'cstuds_client', $client );
	update_post_meta( $post_id, 'cstuds_outcome', $outcome );
}
add_action( 'save_post_case-study', 'cstuds_save_meta_box_data', 10, 2 );
