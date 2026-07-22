<?php
/**
 * Admin page for Social Share Buttons plugin.
 *
 * @package Ssbs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Social Share Buttons settings page.
 *
 * @return void
 */
function ssbs_add_settings_page() {
	add_options_page(
		__( 'Social Share Buttons', 'social-share-buttons' ),
		__( 'Social Share Buttons', 'social-share-buttons' ),
		'manage_options',
		'ssbs-settings',
		'ssbs_render_settings_page'
	);
}
add_action( 'admin_menu', 'ssbs_add_settings_page' );

/**
 * Register settings and fields.
 *
 * @return void
 */
function ssbs_register_settings() {
	register_setting(
		'ssbs_settings_group',
		'ssbs_social_networks',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'ssbs_sanitize_social_networks',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'ssbs_register_settings' );

/**
 * Sanitize social networks input.
 *
 * @param string $input The input string of selected networks.
 * @return string Sanitized comma-separated string.
 */
function ssbs_sanitize_social_networks( $input ) {
	$available  = ssbs_get_available_networks();
	$valid_keys = array_keys( $available );

	// Split input by commas and sanitize each part.
	$input     = explode( ',', $input );
	$sanitized = array();
	foreach ( $input as $network ) {
		$network = sanitize_text_field( $network );
		if ( in_array( $network, $valid_keys, true ) ) {
			$sanitized[] = $network;
		}
	}
	return implode( ',', $sanitized );
}

/**
 * Render the settings page.
 *
 * @return void
 */
function ssbs_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Settings API handles nonce verification.
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'ssbs_messages',
			'ssbs_message',
			__( 'Settings saved.', 'social-share-buttons' ),
			'updated'
		);
	}

	settings_errors( 'ssbs_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'ssbs_settings_group' );
			do_settings_sections( 'ssbs-settings' );
			submit_button( __( 'Save Settings', 'social-share-buttons' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Render social networks field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function ssbs_render_networks_field( $args ) {
	$selected  = explode( ',', get_option( 'ssbs_social_networks', '' ) );
	$available = ssbs_get_available_networks();
	?>
	<fieldset>
		<?php foreach ( $available as $network => $data ) : ?>
			<label>
				<input type="checkbox" name="ssbs_social_networks[]" value="<?php echo esc_attr( $network ); ?>" <?php checked( in_array( $network, $selected, true ) ); ?>>
				<?php echo esc_html( $data['label'] ); ?>
			</label><br>
		<?php endforeach; ?>
		<p class="description"><?php echo esc_html__( 'Select which social networks to display on single posts.', 'social-share-buttons' ); ?></p>
	</fieldset>
	<?php
}

/**
 * Add social networks field to settings page.
 *
 * @return void
 */
function ssbs_add_networks_field() {
	add_settings_field(
		'ssbs_social_networks',
		__( 'Social Networks', 'social-share-buttons' ),
		'ssbs_render_networks_field',
		'ssbs-settings',
		'ssbs_main_section'
	);
}
add_action( 'admin_init', 'ssbs_add_networks_field' );

/**
 * Render section description.
 *
 * @return void
 */
function ssbs_render_section_description() {
	echo '<p>' . esc_html__( 'Select which social networks to display on single posts.', 'social-share-buttons' ) . '</p>';
}

/**
 * Add main settings section.
 *
 * @return void
 */
function ssbs_add_main_section() {
	add_settings_section(
		'ssbs_main_section',
		__( 'Social Networks', 'social-share-buttons' ),
		'ssbs_render_section_description',
		'ssbs-settings'
	);
}
add_action( 'admin_init', 'ssbs_add_main_section' );
