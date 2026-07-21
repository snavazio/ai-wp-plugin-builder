<?php
/**
 * Meta box for testimonial custom fields.
 *
 * @package Tmnl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add meta box for testimonial fields.
 *
 * @return void
 */
function tmnl_add_meta_box() {
	add_meta_box(
		'tmnl_testimonial_details',
		__( 'Testimonial Details', 'testimonials' ),
		'tmnl_render_meta_box',
		'tmnl_testimonial',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tmnl_add_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function tmnl_render_meta_box( $post ) {
	// Add nonce for security.
	wp_nonce_field( 'tmnl_save_meta_box', 'tmnl_meta_box_nonce' );

	// Get current values.
	$rating           = get_post_meta( $post->ID, 'tmnl_rating', true );
	$customer_name    = get_post_meta( $post->ID, 'tmnl_customer_name', true );
	$customer_company = get_post_meta( $post->ID, 'tmnl_customer_company', true );

	?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="tmnl_rating"><?php esc_html_e( 'Star Rating', 'testimonials' ); ?></label>
			</th>
			<td>
				<select name="tmnl_rating" id="tmnl_rating">
					<option value=""><?php esc_html_e( '-- Select Rating --', 'testimonials' ); ?></option>
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<option value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $rating, $i ); ?>>
							<?php echo esc_html( $i . ' ' . _n( 'Star', 'Stars', $i, 'testimonials' ) ); ?>
						</option>
					<?php endfor; ?>
				</select>
				<p class="description">
					<?php esc_html_e( 'Rating from 1 to 5 stars', 'testimonials' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="tmnl_customer_name"><?php esc_html_e( 'Customer Name', 'testimonials' ); ?></label>
			</th>
			<td>
				<input type="text" name="tmnl_customer_name" id="tmnl_customer_name" value="<?php echo esc_attr( $customer_name ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="tmnl_customer_company"><?php esc_html_e( 'Customer Company', 'testimonials' ); ?></label>
			</th>
			<td>
				<input type="text" name="tmnl_customer_company" id="tmnl_customer_company" value="<?php echo esc_attr( $customer_company ); ?>" class="regular-text" />
				<p class="description">
					<?php esc_html_e( 'Optional', 'testimonials' ); ?>
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
function tmnl_save_meta_box( $post_id ) {
	// Check nonce.
	if ( ! isset( $_POST['tmnl_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tmnl_meta_box_nonce'] ) ), 'tmnl_save_meta_box' ) ) {
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

	// Save rating.
	if ( isset( $_POST['tmnl_rating'] ) ) {
		$rating = absint( wp_unslash( $_POST['tmnl_rating'] ) );
		// Clamp rating between 1 and 5.
		if ( $rating >= 1 && $rating <= 5 ) {
			update_post_meta( $post_id, 'tmnl_rating', $rating );
		} elseif ( '' === $_POST['tmnl_rating'] ) {
			delete_post_meta( $post_id, 'tmnl_rating' );
		}
	}

	// Save customer name.
	if ( isset( $_POST['tmnl_customer_name'] ) ) {
		$customer_name = sanitize_text_field( wp_unslash( $_POST['tmnl_customer_name'] ) );
		update_post_meta( $post_id, 'tmnl_customer_name', $customer_name );
	}

	// Save customer company.
	if ( isset( $_POST['tmnl_customer_company'] ) ) {
		$customer_company = sanitize_text_field( wp_unslash( $_POST['tmnl_customer_company'] ) );
		update_post_meta( $post_id, 'tmnl_customer_company', $customer_company );
	}
}
add_action( 'save_post_tmnl_testimonial', 'tmnl_save_meta_box' );
