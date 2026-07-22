<?php
/**
 * Add Event meta box for start date and location.
 *
 * @package Evcal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the event meta box to the event editor.
 *
 * @return void
 */
function evcal_add_event_meta_box() {
	add_meta_box(
		'evcal_event_meta_box',
		__( 'Event Details', 'events-calendar' ),
		'evcal_render_event_meta_box',
		'event',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_event', 'evcal_add_event_meta_box' );

/**
 * Render the event meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function evcal_render_event_meta_box( $post ) {
	$start_date = get_post_meta( $post->ID, 'evcal_start_date', true );
	$location   = get_post_meta( $post->ID, 'evcal_location', true );

	// Nonce for security.
	wp_nonce_field( 'evcal_save_event_meta', 'evcal_event_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="evcal_start_date"><?php esc_html_e( 'Start Date', 'events-calendar' ); ?></label>
				</th>
				<td>
					<input
						type="date"
						id="evcal_start_date"
						name="evcal_start_date"
						value="<?php echo esc_attr( $start_date ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Format: YYYY-MM-DD', 'events-calendar' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="evcal_location"><?php esc_html_e( 'Location', 'events-calendar' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="evcal_location"
						name="evcal_location"
						value="<?php echo esc_attr( $location ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "New York, NY", "Online"', 'events-calendar' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save event meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function evcal_save_event_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['evcal_event_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['evcal_event_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['evcal_event_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'evcal_save_event_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$start_date = isset( $_POST['evcal_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['evcal_start_date'] ) ) : '';
	$location   = isset( $_POST['evcal_location'] ) ? sanitize_text_field( wp_unslash( $_POST['evcal_location'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'evcal_start_date', $start_date );
	update_post_meta( $post_id, 'evcal_location', $location );
}
add_action( 'save_post_event', 'evcal_save_event_meta', 10, 2 );
