<?php
/**
 * Admin page for the Admin Reminder plugin.
 *
 * @package Arrem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Admin Reminder settings page.
 *
 * @return void
 */
function arrem_admin_page_callback() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'admin-reminder' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['arrem_dismiss_notice'] ) && isset( $_POST['nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'arrem_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'admin-reminder' ) );
		}

		// Update user meta to mark notice as dismissed.
		update_user_meta( get_current_user_id(), 'arrem_notice_dismissed', true );
	}

	// Check if notice is dismissed for current user.
	$notice_dismissed = get_user_meta( get_current_user_id(), 'arrem_notice_dismissed', true );

	// Only show notice if not dismissed.
	if ( ! $notice_dismissed ) {
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php
				echo esc_html__( 'This is the Admin Reminder settings page. You can dismiss the admin notice that appears on the dashboard by clicking the button below.', 'admin-reminder' );
				?>
			</p>
			<form method="post">
				<?php wp_nonce_field( 'arrem_nonce', 'nonce' ); ?>
				<input type="hidden" name="arrem_dismiss_notice" value="1" />
				<?php submit_button( __( 'Dismiss Admin Notice', 'admin-reminder' ) ); ?>
			</form>
		</div>
		<?php
	}
}
