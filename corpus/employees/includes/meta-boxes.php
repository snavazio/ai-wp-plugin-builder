<?php
/**
 * Add Employee meta box for department and email.
 *
 * @package Empc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the employee meta box to the employee editor.
 *
 * @return void
 */
function empc_add_employee_meta_box() {
	add_meta_box(
		'empc_employee_meta_box',
		__( 'Employee Details', 'employees' ),
		'empc_render_employee_meta_box',
		'employee',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_employee', 'empc_add_employee_meta_box' );

/**
 * Render the employee meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function empc_render_employee_meta_box( $post ) {
	$department = get_post_meta( $post->ID, 'empc_department', true );
	$email      = get_post_meta( $post->ID, 'empc_email', true );

	// Nonce for security.
	wp_nonce_field( 'empc_save_employee_meta', 'empc_employee_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="empc_department"><?php esc_html_e( 'Department', 'employees' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="empc_department"
						name="empc_department"
						value="<?php echo esc_attr( $department ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "Marketing", "Engineering"', 'employees' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="empc_email"><?php esc_html_e( 'Email', 'employees' ); ?></label>
				</th>
				<td>
					<input
						type="email"
						id="empc_email"
						name="empc_email"
						value="<?php echo esc_attr( $email ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "john@example.com"', 'employees' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save employee meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function empc_save_employee_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['empc_employee_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['empc_employee_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['empc_employee_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'empc_save_employee_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$department = isset( $_POST['empc_department'] ) ? sanitize_text_field( wp_unslash( $_POST['empc_department'] ) ) : '';
	$email      = isset( $_POST['empc_email'] ) ? sanitize_email( wp_unslash( $_POST['empc_email'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'empc_department', $department );
	update_post_meta( $post_id, 'empc_email', $email );
}
add_action( 'save_post_employee', 'empc_save_employee_meta', 10, 2 );
