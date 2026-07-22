<?php
/**
 * Admin page for Social Links Widget plugin.
 *
 * @package Slwgt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Social Links settings page.
 *
 * @return void
 */
function slwgt_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'social-links-widget' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['slwgt_save'] ) && isset( $_POST['slwgt_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['slwgt_nonce'] ) ), 'slwgt_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'social-links-widget' ) );
		}

		// Sanitize input.
		$social_links = array();
		$networks     = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'youtube' );
		foreach ( $networks as $network ) {
			$url = '';
			if ( isset( $_POST[ 'slwgt_' . $network . '_url' ] ) ) {
				$url = esc_url_raw( wp_unslash( $_POST[ 'slwgt_' . $network . '_url' ] ) );
			}
			$social_links[ $network ] = $url;
		}

		// Update option.
		update_option( 'slwgt_social_links', $social_links );
	}

	// Get current social links.
	$social_links = get_option( 'slwgt_social_links', array() );
	$networks     = array(
		'facebook'  => __( 'Facebook', 'social-links-widget' ),
		'twitter'   => __( 'Twitter', 'social-links-widget' ),
		'instagram' => __( 'Instagram', 'social-links-widget' ),
		'linkedin'  => __( 'LinkedIn', 'social-links-widget' ),
		'pinterest' => __( 'Pinterest', 'social-links-widget' ),
		'youtube'   => __( 'YouTube', 'social-links-widget' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Social Links Settings', 'social-links-widget' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'slwgt_settings_nonce', 'slwgt_nonce' ); ?>
			<table class="form-table">
				<?php foreach ( $networks as $network => $name ) : ?>
					<tr>
						<th scope="row">
							<label for="slwgt_<?php echo esc_attr( $network ); ?>_url"><?php echo esc_html( $name ); ?></label>
						</th>
						<td>
							<input type="url" name="slwgt_<?php echo esc_attr( $network ); ?>_url" id="slwgt_<?php echo esc_attr( $network ); ?>_url" value="<?php echo esc_url( $social_links[ $network ] ?? '' ); ?>" class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Enter the full URL of your social profile (e.g., https://facebook.com/yourprofile).', 'social-links-widget' ); ?>
							</p>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Register the Social Links settings page.
 *
 * @return void
 */
function slwgt_register_admin_page() {
	add_options_page(
		__( 'Social Links', 'social-links-widget' ),
		__( 'Social Links', 'social-links-widget' ),
		'manage_options',
		'slwgt-settings',
		'slwgt_admin_page_callback'
	);
}
add_action( 'admin_menu', 'slwgt_register_admin_page' );
