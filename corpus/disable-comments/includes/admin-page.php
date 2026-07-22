<?php
/**
 * Admin page for Disable Comments plugin.
 *
 * @package Dcmn
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Disable Comments settings page.
 *
 * @return void
 */
function dcmn_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'disable-comments' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['dcmn_save'] ) && isset( $_POST['dcmn_nonce'] ) ) {
		// Verify nonce.
		$nonce = sanitize_text_field( wp_unslash( $_POST['dcmn_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'dcmn_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'disable-comments' ) );
		}

		// Sanitize input.
		$enabled = isset( $_POST['dcmn_comments_enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['dcmn_comments_enabled'] ) );

		// Update option.
		update_option( 'dcmn_comments_enabled', $enabled );

		// If enabled, close comments on all posts.
		if ( $enabled ) {
			$all_posts = get_posts(
				array(
					'post_type'      => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
			foreach ( $all_posts as $post_id ) {
				wp_update_post(
					array(
						'ID'             => $post_id,
						'comment_status' => 'closed',
					)
				);
			}
		}
	}

	// Get current setting.
	$enabled = get_option( 'dcmn_comments_enabled', false );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Disable Comments', 'disable-comments' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'dcmn_save', 'dcmn_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="dcmn_comments_enabled">
							<?php esc_html_e( 'Enable Comment Disabling', 'disable-comments' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="dcmn_comments_enabled"
							id="dcmn_comments_enabled"
							value="1"
							<?php checked( $enabled, true ); ?>
						/>
						<p class="description">
							<?php esc_html_e( 'When enabled, closes comments on all post types and hides existing comments.', 'disable-comments' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
