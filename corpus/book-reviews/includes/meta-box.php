<?php
/**
 * Meta box for book review fields.
 *
 * @package Brpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add meta box for book review fields.
 *
 * @return void
 */
function brpl_add_meta_box() {
	add_meta_box(
		'brpl_book_review_details',
		__( 'Book Review Details', 'book-reviews' ),
		'brpl_render_meta_box',
		'book_review',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'brpl_add_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function brpl_render_meta_box( $post ) {
	// Add nonce for security.
	wp_nonce_field( 'brpl_save_meta_box', 'brpl_meta_box_nonce' );

	// Get current values.
	$author = get_post_meta( $post->ID, 'brpl_author', true );
	$rating = get_post_meta( $post->ID, 'brpl_rating', true );

	?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="brpl_author"><?php esc_html_e( 'Author', 'book-reviews' ); ?></label>
			</th>
			<td>
				<input type="text" name="brpl_author" id="brpl_author" value="<?php echo esc_attr( $author ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="brpl_rating"><?php esc_html_e( 'Star Rating', 'book-reviews' ); ?></label>
			</th>
			<td>
				<select name="brpl_rating" id="brpl_rating">
					<option value=""><?php esc_html_e( '-- Select Rating --', 'book-reviews' ); ?></option>
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<option value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $rating, $i ); ?>>
							<?php echo esc_html( $i . ' ' . _n( 'Star', 'Stars', $i, 'book-reviews' ) ); ?>
						</option>
					<?php endfor; ?>
				</select>
				<p class="description">
					<?php esc_html_e( 'Rating from 1 to 5 stars', 'book-reviews' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save the meta box data.
 *
 * @param int $post_id The post ID.
 * @return void
 */
function brpl_save_meta_box( $post_id ) {
	// Check nonce.
	if ( ! isset( $_POST['brpl_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brpl_meta_box_nonce'] ) ), 'brpl_save_meta_box' ) ) {
		return;
	}

	// Check user capabilities.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Save author.
	if ( isset( $_POST['brpl_author'] ) ) {
		$author = sanitize_text_field( wp_unslash( $_POST['brpl_author'] ) );
		update_post_meta( $post_id, 'brpl_author', $author );
	}

	// Save rating.
	if ( isset( $_POST['brpl_rating'] ) ) {
		$rating = absint( wp_unslash( $_POST['brpl_rating'] ) );
		// Clamp rating between 1 and 5.
		if ( $rating >= 1 && $rating <= 5 ) {
			update_post_meta( $post_id, 'brpl_rating', $rating );
		} elseif ( '' === $_POST['brpl_rating'] ) {
			delete_post_meta( $post_id, 'brpl_rating' );
		}
	}
}
add_action( 'save_post_book_review', 'brpl_save_meta_box' );
