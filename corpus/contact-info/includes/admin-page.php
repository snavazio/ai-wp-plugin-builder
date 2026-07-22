<?php
/**
 * Admin page for Contact Info plugin.
 *
 * @package Cinfo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Contact Info settings page.
 *
 * @return void
 */
function cinfo_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'contact-info' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['cinfo_save'] ) && isset( $_POST['cinfo_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cinfo_nonce'] ) ), 'cinfo_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'contact-info' ) );
		}

		// Sanitize input.
		$phone = '';
		if ( isset( $_POST['cinfo_phone'] ) ) {
			$phone = sanitize_text_field( wp_unslash( $_POST['cinfo_phone'] ) );
		}

		$email = '';
		if ( isset( $_POST['cinfo_email'] ) ) {
			$email = sanitize_email( wp_unslash( $_POST['cinfo_email'] ) );
		}

		$address = '';
		if ( isset( $_POST['cinfo_address'] ) ) {
			$address = sanitize_text_field( wp_unslash( $_POST['cinfo_address'] ) );
		}

		// Update option.
		$contact_info = array(
			'phone'   => $phone,
			'email'   => $email,
			'address' => $address,
		);
		update_option( 'cinfo_contact_info', $contact_info );
	}

	// Get current contact info.
	$contact_info = get_option(
		'cinfo_contact_info',
		array(
			'phone'   => '',
			'email'   => '',
			'address' => '',
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contact Info Settings', 'contact-info' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'cinfo_save', 'cinfo_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="cinfo_phone"><?php esc_html_e( 'Phone', 'contact-info' ); ?></label>
					</th>
					<td>
						<input type="text" name="cinfo_phone" id="cinfo_phone" value="<?php echo esc_attr( $contact_info['phone'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the phone number.', 'contact-info' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="cinfo_email"><?php esc_html_e( 'Email', 'contact-info' ); ?></label>
					</th>
					<td>
						<input type="email" name="cinfo_email" id="cinfo_email" value="<?php echo esc_attr( $contact_info['email'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Enter the email address.', 'contact-info' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="cinfo_address"><?php esc_html_e( 'Address', 'contact-info' ); ?></label>
					</th>
					<td>
						<textarea name="cinfo_address" id="cinfo_address" rows="5" class="large-text"><?php echo esc_textarea( $contact_info['address'] ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter the address.', 'contact-info' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Register the Contact Info settings page.
 *
 * @return void
 */
function cinfo_register_admin_page() {
	add_options_page(
		__( 'Contact Info', 'contact-info' ),
		__( 'Contact Info', 'contact-info' ),
		'manage_options',
		'contact-info',
		'cinfo_admin_page_callback'
	);
}
add_action( 'admin_menu', 'cinfo_register_admin_page' );
