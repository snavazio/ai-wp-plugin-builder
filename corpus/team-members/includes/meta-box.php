<?php
/**
 * Meta box for team member fields.
 *
 * @package Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add meta box for team member fields.
 *
 * @return void
 */
function team_add_meta_box() {
	add_meta_box(
		'team_member_details',
		__( 'Team Member Details', 'team-members' ),
		'team_render_meta_box',
		'team_member',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes_team_member', 'team_add_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function team_render_meta_box( $post ) {
	// Add nonce for security.
	wp_nonce_field( 'team_save_meta_box', 'team_meta_box_nonce' );

	// Get current values.
	$role  = get_post_meta( $post->ID, 'team_role', true );
	$email = get_post_meta( $post->ID, 'team_email', true );

	?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="team_role"><?php esc_html_e( 'Role', 'team-members' ); ?></label>
			</th>
			<td>
				<input type="text" name="team_role" id="team_role" value="<?php echo esc_attr( $role ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="team_email"><?php esc_html_e( 'Email', 'team-members' ); ?></label>
			</th>
			<td>
				<input type="email" name="team_email" id="team_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text" />
				<p class="description">
					<?php esc_html_e( 'Email address for contact', 'team-members' ); ?>
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
function team_save_meta_box( $post_id ) {
	// Check nonce.
	if ( ! isset( $_POST['team_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['team_meta_box_nonce'] ) ), 'team_save_meta_box' ) ) {
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

	// Save role.
	if ( isset( $_POST['team_role'] ) ) {
		$role = sanitize_text_field( wp_unslash( $_POST['team_role'] ) );
		update_post_meta( $post_id, 'team_role', $role );
	}

	// Save email.
	if ( isset( $_POST['team_email'] ) ) {
		$email = sanitize_email( wp_unslash( $_POST['team_email'] ) );
		update_post_meta( $post_id, 'team_email', $email );
	}
}
add_action( 'save_post_team_member', 'team_save_meta_box' );
