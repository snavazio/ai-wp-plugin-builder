<?php
/**
 * Add Product meta box for price and sku.
 *
 * @package Strp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the product meta box to the product editor.
 *
 * @return void
 */
function strp_add_product_meta_box() {
	add_meta_box(
		'strp_product_meta_box',
		__( 'Product Details', 'store-products' ),
		'strp_render_product_meta_box',
		'product',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_product', 'strp_add_product_meta_box' );

/**
 * Render the product meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function strp_render_product_meta_box( $post ) {
	$price = get_post_meta( $post->ID, 'strp_price', true );
	$sku   = get_post_meta( $post->ID, 'strp_sku', true );

	// Nonce for security.
	wp_nonce_field( 'strp_save_product_meta', 'strp_product_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="strp_price"><?php esc_html_e( 'Price', 'store-products' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="strp_price"
						name="strp_price"
						value="<?php echo esc_attr( $price ); ?>"
						min="0"
						step="0.01"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Product price (e.g., 19.99)', 'store-products' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="strp_sku"><?php esc_html_e( 'SKU', 'store-products' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="strp_sku"
						name="strp_sku"
						value="<?php echo esc_attr( $sku ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'Stock Keeping Unit (e.g., PROD-123)', 'store-products' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save product meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function strp_save_product_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['strp_product_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['strp_product_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['strp_product_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'strp_save_product_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$price = isset( $_POST['strp_price'] ) ? sanitize_text_field( wp_unslash( $_POST['strp_price'] ) ) : '';
	$sku   = isset( $_POST['strp_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['strp_sku'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'strp_price', $price );
	update_post_meta( $post_id, 'strp_sku', $sku );
}
add_action( 'save_post_product', 'strp_save_product_meta', 10, 2 );
