<?php
/**
 * Admin page for Company Footer Settings plugin.
 *
 * @package Cfst
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Company Footer Settings page.
 *
 * @return void
 */
function cfst_add_settings_page() {
	add_options_page(
		__( 'Company Footer Settings', 'company-footer-settings' ),
		__( 'Company Footer Settings', 'company-footer-settings' ),
		'manage_options',
		'cfst-settings',
		'cfst_render_settings_page'
	);
}
add_action( 'admin_menu', 'cfst_add_settings_page' );

/**
 * Register settings and fields.
 *
 * @return void
 */
function cfst_register_settings() {
	register_setting(
		'cfst_settings_group',
		'cfst_company_name',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'cfst_sanitize_company_name',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'cfst_register_settings' );

/**
 * Sanitize company name input.
 *
 * @param string $value The company name value.
 * @return string Sanitized value.
 */
function cfst_sanitize_company_name( $value ) {
	return sanitize_text_field( $value );
}

/**
 * Render the settings page.
 *
 * @return void
 */
function cfst_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Settings API handles nonce verification.
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'cfst_messages',
			'cfst_message',
			__( 'Settings saved.', 'company-footer-settings' ),
			'updated'
		);
	}

	settings_errors( 'cfst_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'cfst_settings_group' );
			do_settings_sections( 'cfst-settings' );
			submit_button( __( 'Save Settings', 'company-footer-settings' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Render company name field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function cfst_render_company_name_field( $args ) {
	$value = get_option( 'cfst_company_name', '' );
	printf(
		'<input type="text" id="cfst_company_name" name="cfst_company_name" value="%s" class="regular-text" placeholder="%s" />',
		esc_attr( $value ),
		esc_attr__( 'e.g., Acme Corp', 'company-footer-settings' )
	);
	echo '<p class="description">' . esc_html__( 'Enter the company name to display in the footer copyright notice.', 'company-footer-settings' ) . '</p>';
}

/**
 * Add company name field to settings page.
 *
 * @return void
 */
function cfst_add_company_name_field() {
	add_settings_field(
		'cfst_company_name',
		__( 'Company Name', 'company-footer-settings' ),
		'cfst_render_company_name_field',
		'cfst-settings',
		'cfst_main_section'
	);
}
add_action( 'admin_init', 'cfst_add_company_name_field' );

/**
 * Render section description.
 *
 * @return void
 */
function cfst_render_section_description() {
	echo '<p>' . esc_html__( 'Set the company name to display in the footer copyright notice.', 'company-footer-settings' ) . '</p>';
}

/**
 * Add main settings section.
 *
 * @return void
 */
function cfst_add_main_section() {
	add_settings_section(
		'cfst_main_section',
		__( 'Company Name Settings', 'company-footer-settings' ),
		'cfst_render_section_description',
		'cfst-settings'
	);
}
add_action( 'admin_init', 'cfst_add_main_section' );
