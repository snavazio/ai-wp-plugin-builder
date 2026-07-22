<?php
/**
 * Admin page for SEO Defaults plugin.
 *
 * @package Seod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the SEO Defaults settings page.
 *
 * @return void
 */
function seod_add_settings_page() {
	add_options_page(
		__( 'SEO Defaults', 'seo-defaults' ),
		__( 'SEO Defaults', 'seo-defaults' ),
		'manage_options',
		'seod-settings',
		'seod_render_settings_page'
	);
}
add_action( 'admin_menu', 'seod_add_settings_page' );

/**
 * Register settings and fields.
 *
 * @return void
 */
function seod_register_settings() {
	register_setting(
		'seod_settings_group',
		'seod_default_meta_description',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'seod_sanitize_meta_description',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'seod_register_settings' );

/**
 * Sanitize meta description input.
 *
 * @param string $value The meta description value.
 * @return string Sanitized value.
 */
function seod_sanitize_meta_description( $value ) {
	return sanitize_text_field( $value );
}

/**
 * Render the settings page.
 *
 * @return void
 */
function seod_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Settings API handles nonce verification.
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'seod_messages',
			'seod_message',
			__( 'Settings saved.', 'seo-defaults' ),
			'updated'
		);
	}

	settings_errors( 'seod_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'seod_settings_group' );
			do_settings_sections( 'seod-settings' );
			submit_button( __( 'Save Settings', 'seo-defaults' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Render meta description field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function seod_render_meta_description_field( $args ) {
	$value = get_option( 'seod_default_meta_description', '' );
	printf(
		'<textarea name="seod_default_meta_description" id="seod_default_meta_description" rows="4" class="large-text">%s</textarea>',
		esc_textarea( $value )
	);
	echo '<p class="description">' . esc_html__( 'Enter the default meta description to output in wp_head when missing.', 'seo-defaults' ) . '</p>';
}

/**
 * Add meta description field to settings page.
 *
 * @return void
 */
function seod_add_meta_description_field() {
	add_settings_field(
		'seod_default_meta_description',
		__( 'Default Meta Description', 'seo-defaults' ),
		'seod_render_meta_description_field',
		'seod-settings',
		'seod_main_section'
	);
}
add_action( 'admin_init', 'seod_add_meta_description_field' );

/**
 * Render section description.
 *
 * @return void
 */
function seod_render_section_description() {
	echo '<p>' . esc_html__( 'Set a default meta description to output in the head when no description is provided by a plugin or post.', 'seo-defaults' ) . '</p>';
}

/**
 * Add main settings section.
 *
 * @return void
 */
function seod_add_main_section() {
	add_settings_section(
		'seod_main_section',
		__( 'Default Meta Description', 'seo-defaults' ),
		'seod_render_section_description',
		'seod-settings'
	);
}
add_action( 'admin_init', 'seod_add_main_section' );
