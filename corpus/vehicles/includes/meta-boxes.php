<?php
/**
 * Add Vehicle meta box for make, model, and year.
 *
 * @package Vclt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the vehicle meta box to the vehicle editor.
 *
 * @return void
 */
function vclt_add_vehicle_meta_box() {
	add_meta_box(
		'vclt_vehicle_meta_box',
		__( 'Vehicle Details', 'vehicles' ),
		'vclt_render_vehicle_meta_box',
		'vehicle',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_vehicle', 'vclt_add_vehicle_meta_box' );

/**
 * Render the vehicle meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function vclt_render_vehicle_meta_box( $post ) {
	$make  = get_post_meta( $post->ID, 'vclt_make', true );
	$model = get_post_meta( $post->ID, 'vclt_model', true );
	$year  = get_post_meta( $post->ID, 'vclt_year', true );

	// Nonce for security.
	wp_nonce_field( 'vclt_save_vehicle_meta', 'vclt_vehicle_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="vclt_make"><?php esc_html_e( 'Make', 'vehicles' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="vclt_make"
						name="vclt_make"
						value="<?php echo esc_attr( $make ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "Toyota", "Ford"', 'vehicles' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="vclt_model"><?php esc_html_e( 'Model', 'vehicles' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="vclt_model"
						name="vclt_model"
						value="<?php echo esc_attr( $model ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "Camry", "F-150"', 'vehicles' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="vclt_year"><?php esc_html_e( 'Year', 'vehicles' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="vclt_year"
						name="vclt_year"
						value="<?php echo esc_attr( $year ); ?>"
						min="1900"
						max="2099"
						class="small-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., 2020', 'vehicles' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save vehicle meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function vclt_save_vehicle_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['vclt_vehicle_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['vclt_vehicle_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['vclt_vehicle_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'vclt_save_vehicle_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize inputs.
	$make  = isset( $_POST['vclt_make'] ) ? sanitize_text_field( wp_unslash( $_POST['vclt_make'] ) ) : '';
	$model = isset( $_POST['vclt_model'] ) ? sanitize_text_field( wp_unslash( $_POST['vclt_model'] ) ) : '';
	$year  = isset( $_POST['vclt_year'] ) ? absint( wp_unslash( $_POST['vclt_year'] ) ) : 0;

	// Update meta.
	update_post_meta( $post_id, 'vclt_make', $make );
	update_post_meta( $post_id, 'vclt_model', $model );
	update_post_meta( $post_id, 'vclt_year', $year );
}
add_action( 'save_post_vehicle', 'vclt_save_vehicle_meta', 10, 2 );
