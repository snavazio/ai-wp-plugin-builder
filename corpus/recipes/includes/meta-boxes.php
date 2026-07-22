<?php
/**
 * Add Recipe meta box for prep time and servings.
 *
 * @package Rcpr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the recipe meta box to the recipe editor.
 *
 * @return void
 */
function rcpr_add_recipe_meta_box() {
	add_meta_box(
		'rcpr_recipe_meta_box',
		__( 'Recipe Details', 'recipes' ),
		'rcpr_render_recipe_meta_box',
		'recipe',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_recipe', 'rcpr_add_recipe_meta_box' );

/**
 * Render the recipe meta box HTML.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function rcpr_render_recipe_meta_box( $post ) {
	$prep_time = get_post_meta( $post->ID, 'rcpr_prep_time', true );
	$servings  = get_post_meta( $post->ID, 'rcpr_servings', true );

	// Nonce for security.
	wp_nonce_field( 'rcpr_save_recipe_meta', 'rcpr_recipe_meta_nonce' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="rcpr_prep_time"><?php esc_html_e( 'Prep Time', 'recipes' ); ?></label>
				</th>
				<td>
					<input
						type="text"
						id="rcpr_prep_time"
						name="rcpr_prep_time"
						value="<?php echo esc_attr( $prep_time ); ?>"
						class="regular-text"
					/>
					<p class="description"><?php esc_html_e( 'e.g., "30 minutes", "1 hour"', 'recipes' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="rcpr_servings"><?php esc_html_e( 'Servings', 'recipes' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="rcpr_servings"
						name="rcpr_servings"
						value="<?php echo esc_attr( $servings ); ?>"
						min="1"
						class="small-text"
					/>
					<p class="description"><?php esc_html_e( 'Number of servings', 'recipes' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Save recipe meta box data.
 *
 * @param int     $post_id The post ID.
 * @param WP_Post $post    The post object.
 * @return void
 */
function rcpr_save_recipe_meta( $post_id, $post ) {
	// Verify nonce.
	$nonce = isset( $_POST['rcpr_recipe_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['rcpr_recipe_meta_nonce'] ) ) : '';
	if ( ! isset( $_POST['rcpr_recipe_meta_nonce'] ) || ! wp_verify_nonce( $nonce, 'rcpr_save_recipe_meta' ) ) {
		return;
	}

	// Check capability.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize input.
	$prep_time = isset( $_POST['rcpr_prep_time'] ) ? sanitize_text_field( wp_unslash( $_POST['rcpr_prep_time'] ) ) : '';
	$servings  = isset( $_POST['rcpr_servings'] ) ? absint( wp_unslash( $_POST['rcpr_servings'] ) ) : 0;

	// Update meta.
	update_post_meta( $post_id, 'rcpr_prep_time', $prep_time );
	update_post_meta( $post_id, 'rcpr_servings', $servings );
}
add_action( 'save_post_recipe', 'rcpr_save_recipe_meta', 10, 2 );
