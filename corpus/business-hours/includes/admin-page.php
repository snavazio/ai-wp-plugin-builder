<?php
/**
 * Admin page for Business Hours plugin.
 *
 * @package Bhrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for the Business Hours settings page.
 *
 * @return void
 */
function bhrs_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'business-hours' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['bhrs_save'] ) && isset( $_POST['bhrs_nonce'] ) ) {
		// Verify nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bhrs_nonce'] ) ), 'bhrs_save' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'business-hours' ) );
		}

		// Sanitize input.
		$business_hours = array();
		$days           = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );

		foreach ( $days as $day ) {
			$open = '';
			if ( isset( $_POST[ "bhrs_{$day}_open" ] ) ) {
				$open = sanitize_text_field( wp_unslash( $_POST[ "bhrs_{$day}_open" ] ) );
			}

			$close = '';
			if ( isset( $_POST[ "bhrs_{$day}_close" ] ) ) {
				$close = sanitize_text_field( wp_unslash( $_POST[ "bhrs_{$day}_close" ] ) );
			}

			$business_hours[ $day ] = array(
				'open'  => $open,
				'close' => $close,
			);
		}

		// Update option.
		update_option( 'bhrs_business_hours', $business_hours );
	}

	// Get current business hours.
	$business_hours = get_option( 'bhrs_business_hours', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Business Hours Settings', 'business-hours' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'bhrs_save', 'bhrs_nonce' ); ?>
			<table class="form-table">
				<?php
				$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
				foreach ( $days as $day ) :
					$open  = isset( $business_hours[ $day ]['open'] ) ? $business_hours[ $day ]['open'] : '';
					$close = isset( $business_hours[ $day ]['close'] ) ? $business_hours[ $day ]['close'] : '';
					?>
					<tr>
						<th scope="row">
							<label for="bhrs_<?php echo esc_attr( $day ); ?>_open"><?php echo esc_html( ucfirst( $day ) ); ?></label>
						</th>
						<td>
							<input type="time" name="bhrs_<?php echo esc_attr( $day ); ?>_open" id="bhrs_<?php echo esc_attr( $day ); ?>_open" value="<?php echo esc_attr( $open ); ?>" class="regular-text" />
							<span class="description"><?php esc_html_e( 'Open time', 'business-hours' ); ?></span>
						</td>
						<td>
							<input type="time" name="bhrs_<?php echo esc_attr( $day ); ?>_close" id="bhrs_<?php echo esc_attr( $day ); ?>_close" value="<?php echo esc_attr( $close ); ?>" class="regular-text" />
							<span class="description"><?php esc_html_e( 'Close time', 'business-hours' ); ?></span>
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
 * Register the Business Hours settings page.
 *
 * @return void
 */
function bhrs_register_admin_page() {
	add_options_page(
		__( 'Business Hours', 'business-hours' ),
		__( 'Business Hours', 'business-hours' ),
		'manage_options',
		'business-hours-settings',
		'bhrs_admin_page_callback'
	);
}
add_action( 'admin_menu', 'bhrs_register_admin_page' );
