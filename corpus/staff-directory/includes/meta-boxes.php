<?php
/**
 * Add Staff meta box for job title and phone.
 *
 * @package Staff
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the staff meta box to the staff editor.
 *
 * @return void
 */
function staff_add_staff_meta_box() {
	add_meta_box(
		'staff_staff_meta_box',
		__( 'Staff Details', 'staff-directory' ),
		'staff_render_staff_meta_box',
		'staff',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_staff', 'staff_add_staff_meta_box' );

/**
 * Render the staff meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function staff_render_staff_meta_box( $post ) {
	$job_title = get_post_meta( $post->ID, 'staff_job_title', true );
	$phone     = get_post_meta( $post->ID, 'staff_phone', true );

	// Nonce for security.
	wp_nonce_field( 'staff_save_staff_meta', 'staff_staff_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="staff_job_title"><?php esc_html_e( 'Job Title', 'staff-directory' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="staff_job_title"
						name="staff_job_title"
						value="<?php echo esc_attr( $job_title ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "Senior Developer", "Marketing Manager"', 'staff-directory' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="staff_phone"><?php esc_html_e( 'Phone Number', 'staff-directory' ); ?></label>
				</th>
				<td>
					<input
						type="tel"
						id="staff_phone"
						name="staff_phone"
						value="<?php echo esc_attr( $phone ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "+1 (555) 123-4567"', 'staff-directory' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save staff meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function staff_save_staff_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['staff_staff_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['staff_staff_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['staff_staff_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'staff_save_staff_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$job_title = isset( $_POST['staff_job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['staff_job_title'] ) ) : '';
	$phone     = isset( $_POST['staff_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['staff_phone'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'staff_job_title', $job_title );
	update_post_meta( $post_id, 'staff_phone', $phone );
}
add_action( 'save_post_staff', 'staff_save_staff_meta', 10, 2 );
