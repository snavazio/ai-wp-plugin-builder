<?php
/**
 * Add Rental meta box for price and bedrooms.
 *
 * @package Rent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the rental meta box to the rental editor.
 *
 * @return void
 */
function rent_add_rental_meta_box() {
	add_meta_box(
		'rent_rental_meta_box',
		__( 'Rental Details', 'rentals' ),
		'rent_render_rental_meta_box',
		'rental',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_rental', 'rent_add_rental_meta_box' );

/**
 * Render the rental meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function rent_render_rental_meta_box( $post ) {
	$price    = get_post_meta( $post->ID, 'rent_price', true );
	$bedrooms = get_post_meta( $post->ID, 'rent_bedrooms', true );

	// Nonce for security.
	wp_nonce_field( 'rent_save_rental_meta', 'rent_rental_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="rent_price"><?php esc_html_e( 'Price', 'rentals' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="rent_price"
						name="rent_price"
						value="<?php echo esc_attr( $price ); ?>"
						min="0"
						step="0.01"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Monthly rental price (e.g., 1200.00)', 'rentals' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="rent_bedrooms"><?php esc_html_e( 'Bedrooms', 'rentals' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="rent_bedrooms"
						name="rent_bedrooms"
						value="<?php echo esc_attr( $bedrooms ); ?>"
						min="0"
						class="small-text"
					/>
					<p class="description"><?php esc_html_e( 'Number of bedrooms', 'rentals' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save rental meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function rent_save_rental_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['rent_rental_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rental_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['rent_rental_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'rent_save_rental_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$price    = isset( $_POST['rent_price'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_price'] ) ) : '';
	$bedrooms = isset( $_POST['rent_bedrooms'] ) ? absint( wp_unslash( $_POST['rent_bedrooms'] ) ) : 0;

	// Update meta.
	update_post_meta( $post_id, 'rent_price', $price );
	update_post_meta( $post_id, 'rent_bedrooms', $bedrooms );
}
add_action( 'save_post_rental', 'rent_save_rental_meta', 10, 2 );
