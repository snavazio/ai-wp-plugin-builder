<?php
/**
 * Add Coupon meta boxes for code and expiry date.
 *
 * @package Cpns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the coupon meta box to the editor.
 *
 * @return void
 */
function cpns_add_meta_boxes() {
	add_meta_box(
		'cpns_coupon_meta_box',
		__( 'Coupon Details', 'coupons' ),
		'cpns_render_meta_box',
		'coupon',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_coupon', 'cpns_add_meta_boxes' );

/**
 * Render the coupon meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function cpns_render_meta_box( $post ) {
	$code   = get_post_meta( $post->ID, 'cpns_code', true );
	$expiry = get_post_meta( $post->ID, 'cpns_expiry', true );

	// Nonce for security.
	wp_nonce_field( 'cpns_save_coupon_meta', 'cpns_coupon_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="cpns_code"><?php esc_html_e( 'Coupon Code', 'coupons' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="cpns_code"
						name="cpns_code"
						value="<?php echo esc_attr( $code ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Unique coupon code (e.g., SAVE20)', 'coupons' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpns_expiry"><?php esc_html_e( 'Expiry Date', 'coupons' ); ?></label>
				</th>
				<td>
					<input
						type="date"
						id="cpns_expiry"
						name="cpns_expiry"
						value="<?php echo esc_attr( $expiry ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Date when coupon expires (YYYY-MM-DD)', 'coupons' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save coupon meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function cpns_save_meta_box_data( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['cpns_coupon_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cpns_coupon_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['cpns_coupon_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'cpns_save_coupon_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$code   = isset( $_POST['cpns_code'] ) ? sanitize_text_field( wp_unslash( $_POST['cpns_code'] ) ) : '';
	$expiry = isset( $_POST['cpns_expiry'] ) ? sanitize_text_field( wp_unslash( $_POST['cpns_expiry'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'cpns_code', $code );
	update_post_meta( $post_id, 'cpns_expiry', $expiry );
}
add_action( 'save_post_coupon', 'cpns_save_meta_box_data', 10, 2 );
