<?php
/**
 * Add Restaurant Menu meta box for price and dietary notes.
 *
 * @package Rmenu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the menu item meta box to the menu item editor.
 *
 * @return void
 */
function rmenu_add_menu_item_meta_box() {
	add_meta_box(
		'rmenu_menu_item_meta_box',
		__( 'Menu Details', 'restaurant-menu' ),
		'rmenu_render_menu_item_meta_box',
		'menu_item',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_menu_item', 'rmenu_add_menu_item_meta_box' );

/**
 * Render the menu item meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function rmenu_render_menu_item_meta_box( $post ) {
	$price   = get_post_meta( $post->ID, 'rmenu_price', true );
	$dietary = get_post_meta( $post->ID, 'rmenu_dietary_notes', true );

	// Nonce for security.
	wp_nonce_field( 'rmenu_save_menu_item_meta', 'rmenu_menu_item_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="rmenu_price"><?php esc_html_e( 'Price', 'restaurant-menu' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="rmenu_price"
						name="rmenu_price"
						value="<?php echo esc_attr( $price ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "$12.99", "Free"', 'restaurant-menu' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="rmenu_dietary_notes"><?php esc_html_e( 'Dietary Notes', 'restaurant-menu' ); ?></label>
				</th>
				<td>
					<textarea
						id="rmenu_dietary_notes"
						name="rmenu_dietary_notes"
						rows="3"
						class="large-text"
					><?php echo esc_textarea( $dietary ); ?></textarea>
					<p class="description"><?php esc_html_e( 'e.g., "Gluten-free", "Vegetarian", "Contains nuts"', 'restaurant-menu' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save menu item meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function rmenu_save_menu_item_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['rmenu_menu_item_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['rmenu_menu_item_nonce'] ) ) : '';
	if ( ! isset( $_POST['rmenu_menu_item_nonce'] ) || ! wp_verify_nonce( $nonce, 'rmenu_save_menu_item_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$price   = isset( $_POST['rmenu_price'] ) ? sanitize_text_field( wp_unslash( $_POST['rmenu_price'] ) ) : '';
	$dietary = isset( $_POST['rmenu_dietary_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rmenu_dietary_notes'] ) ) : '';

	// Update meta.
	update_post_meta( $post_id, 'rmenu_price', $price );
	update_post_meta( $post_id, 'rmenu_dietary_notes', $dietary );
}
add_action( 'save_post_menu_item', 'rmenu_save_menu_item_meta', 10, 2 );
