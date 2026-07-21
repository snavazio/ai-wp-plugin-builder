<?php
/**
 * Admin settings page for Business Hours.
 *
 * @package Bhrs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add settings page to admin menu.
 *
 * @return void
 */
function bhrs_add_settings_page() {
	add_options_page(
		__( 'Business Hours', 'business-hours' ),
		__( 'Business Hours', 'business-hours' ),
		'manage_options',
		'bhrs-settings',
		'bhrs_render_settings_page'
	);
}
add_action( 'admin_menu', 'bhrs_add_settings_page' );

/**
 * Register settings and fields.
 *
 * @return void
 */
function bhrs_register_settings() {
	$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );

	foreach ( $days as $day ) {
		register_setting(
			'bhrs_settings_group',
			'bhrs_' . $day . '_open',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'bhrs_settings_group',
			'bhrs_' . $day . '_close',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'bhrs_settings_group',
			'bhrs_' . $day . '_closed',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'bhrs_sanitize_checkbox',
				'default'           => false,
			)
		);
	}

	add_settings_section(
		'bhrs_main_section',
		__( 'Business Hours Configuration', 'business-hours' ),
		'bhrs_render_section_description',
		'bhrs-settings'
	);

	foreach ( $days as $day ) {
		$day_label = ucfirst( $day );

		add_settings_field(
			'bhrs_' . $day . '_open',
			/* translators: %s: Day of the week name */
			sprintf( __( '%s Open', 'business-hours' ), $day_label ),
			'bhrs_render_time_field',
			'bhrs-settings',
			'bhrs_main_section',
			array( 'label_for' => 'bhrs_' . $day . '_open' )
		);

		add_settings_field(
			'bhrs_' . $day . '_close',
			/* translators: %s: Day of the week name */
			sprintf( __( '%s Close', 'business-hours' ), $day_label ),
			'bhrs_render_time_field',
			'bhrs-settings',
			'bhrs_main_section',
			array( 'label_for' => 'bhrs_' . $day . '_close' )
		);

		add_settings_field(
			'bhrs_' . $day . '_closed',
			/* translators: %s: Day of the week name */
			sprintf( __( '%s Closed', 'business-hours' ), $day_label ),
			'bhrs_render_checkbox_field',
			'bhrs-settings',
			'bhrs_main_section',
			array( 'label_for' => 'bhrs_' . $day . '_closed' )
		);
	}
}
add_action( 'admin_init', 'bhrs_register_settings' );

/**
 * Sanitize checkbox value.
 *
 * @param mixed $value The value to sanitize.
 * @return bool
 */
function bhrs_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Render section description.
 *
 * @return void
 */
function bhrs_render_section_description() {
	echo '<p>' . esc_html__( 'Set your business operating hours for each day of the week. Use time format like "9:00 AM" or "5:00 PM". Check "Closed" if you are not open that day.', 'business-hours' ) . '</p>';
}

/**
 * Render time input field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function bhrs_render_time_field( $args ) {
	$option_name = $args['label_for'];
	$value       = get_option( $option_name, '' );
	printf(
		'<input type="text" id="%s" name="%s" value="%s" class="regular-text" placeholder="%s" />',
		esc_attr( $option_name ),
		esc_attr( $option_name ),
		esc_attr( $value ),
		esc_attr__( 'e.g., 9:00 AM', 'business-hours' )
	);
}

/**
 * Render checkbox field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function bhrs_render_checkbox_field( $args ) {
	$option_name = $args['label_for'];
	$value       = get_option( $option_name, false );
	printf(
		'<input type="checkbox" id="%s" name="%s" value="1" %s />',
		esc_attr( $option_name ),
		esc_attr( $option_name ),
		checked( 1, $value, false )
	);
}

/**
 * Render the settings page.
 *
 * @return void
 */
function bhrs_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Settings API handles nonce verification.
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'bhrs_messages',
			'bhrs_message',
			__( 'Settings saved.', 'business-hours' ),
			'updated'
		);
	}

	settings_errors( 'bhrs_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'bhrs_settings_group' );
			do_settings_sections( 'bhrs-settings' );
			submit_button( __( 'Save Settings', 'business-hours' ) );
			?>
		</form>
	</div>
	<?php
}
