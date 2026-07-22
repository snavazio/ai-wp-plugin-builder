<?php
/**
 * Add Job meta box for location, employment type, and salary range.
 *
 * @package Jobl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the job meta box to the job editor.
 *
 * @return void
 */
function jobl_add_job_meta_box() {
	add_meta_box(
		'jobl_job_meta_box',
		__( 'Job Details', 'job-listings' ),
		'jobl_render_job_meta_box',
		'job',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_job', 'jobl_add_job_meta_box' );

/**
 * Render the job meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function jobl_render_job_meta_box( $post ) {
	$location     = get_post_meta( $post->ID, 'jobl_location', true );
	$employment   = get_post_meta( $post->ID, 'jobl_employment_type', true );
	$salary_range = get_post_meta( $post->ID, 'jobl_salary_range', true );

	// Nonce for security.
	wp_nonce_field( 'jobl_save_job_meta', 'jobl_job_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="jobl_location"><?php esc_html_e( 'Location', 'job-listings' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="jobl_location"
						name="jobl_location"
						value="<?php echo esc_attr( $location ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "New York, NY", "Remote"', 'job-listings' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jobl_employment_type"><?php esc_html_e( 'Employment Type', 'job-listings' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="jobl_employment_type"
						name="jobl_employment_type"
						value="<?php echo esc_attr( $employment ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "Full-time", "Part-time", "Contract"', 'job-listings' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jobl_salary_range"><?php esc_html_e( 'Salary Range', 'job-listings' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="jobl_salary_range"
						name="jobl_salary_range"
						value="<?php echo esc_attr( $salary_range ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "$50,000 - $70,000", "Competitive"', 'job-listings' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save job meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function jobl_save_job_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['jobl_job_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['jobl_job_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['jobl_job_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'jobl_save_job_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$location     = isset( $_POST['jobl_location'] ) ? sanitize_text_field( wp_unslash( $_POST['jobl_location'] ) ) : '';
	$employment   = isset( $_POST['jobl_employment_type'] ) ? sanitize_text_field( wp_unslash( $_POST['jobl_employment_type'] ) ) : '';
	$salary_range = isset( $_POST['jobl_salary_range'] ) ? sanitize_text_field( wp_unslash( $_POST['jobl_salary_range'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'jobl_location', $location );
	update_post_meta( $post_id, 'jobl_employment_type', $employment );
	update_post_meta( $post_id, 'jobl_salary_range', $salary_range );
}
add_action( 'save_post_job', 'jobl_save_job_meta', 10, 2 );
