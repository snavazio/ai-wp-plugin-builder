<?php
/**
 * Settings screen: backend service URL, API key, and generation defaults.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers plugin settings and renders the settings submenu page.
 */
class Aiwpb_Settings {

	const GROUP = 'aiwpb_settings';

	/**
	 * Wire up hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ), 20 );
	}

	/**
	 * Register options with sanitizing callbacks.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			self::GROUP,
			'aiwpb_backend_url',
			array(
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);
		register_setting(
			self::GROUP,
			'aiwpb_api_key',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		register_setting(
			self::GROUP,
			'aiwpb_default_engine',
			array(
				'sanitize_callback' => array( $this, 'sanitize_engine' ),
				'default'           => 'claude',
			)
		);
		register_setting(
			self::GROUP,
			'aiwpb_local_model',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'qwen3:30b',
			)
		);
	}

	/**
	 * Restrict engine to the known values.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	public function sanitize_engine( $value ) {
		return ( 'local' === $value ) ? 'local' : 'claude';
	}

	/**
	 * Add the settings submenu under the main plugin menu.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'ai-plugin-builder',
			__( 'AI Builder Settings', 'ai-plugin-builder' ),
			__( 'Settings', 'ai-plugin-builder' ),
			'manage_options',
			'ai-plugin-builder-settings',
			array( $this, 'render' )
		);
	}

	/**
	 * Render the settings form.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$engine = get_option( 'aiwpb_default_engine', 'claude' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'AI Plugin Builder — Settings', 'ai-plugin-builder' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( self::GROUP ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="aiwpb_backend_url"><?php echo esc_html__( 'Builder service URL', 'ai-plugin-builder' ); ?></label></th>
						<td>
							<input name="aiwpb_backend_url" id="aiwpb_backend_url" type="url" class="regular-text" placeholder="http://localhost:8787"
								value="<?php echo esc_attr( get_option( 'aiwpb_backend_url', '' ) ); ?>" />
							<p class="description"><?php echo esc_html__( 'Base URL of the AI WP Plugin Builder service (the machine running `npm run serve`).', 'ai-plugin-builder' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="aiwpb_api_key"><?php echo esc_html__( 'API key', 'ai-plugin-builder' ); ?></label></th>
						<td>
							<input name="aiwpb_api_key" id="aiwpb_api_key" type="password" autocomplete="off" class="regular-text"
								value="<?php echo esc_attr( get_option( 'aiwpb_api_key', '' ) ); ?>" />
							<p class="description"><?php echo esc_html__( 'Must match AIWPB_API_KEY on the service.', 'ai-plugin-builder' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Default engine', 'ai-plugin-builder' ); ?></th>
						<td>
							<select name="aiwpb_default_engine">
								<option value="claude" <?php selected( $engine, 'claude' ); ?>><?php echo esc_html__( 'Claude (Agent SDK)', 'ai-plugin-builder' ); ?></option>
								<option value="local" <?php selected( $engine, 'local' ); ?>><?php echo esc_html__( 'Local (Ollama, $0)', 'ai-plugin-builder' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="aiwpb_local_model"><?php echo esc_html__( 'Local model', 'ai-plugin-builder' ); ?></label></th>
						<td>
							<input name="aiwpb_local_model" id="aiwpb_local_model" type="text" class="regular-text"
								value="<?php echo esc_attr( get_option( 'aiwpb_local_model', 'qwen3:30b' ) ); ?>" />
							<p class="description"><?php echo esc_html__( 'Ollama model used for the local engine, e.g. qwen3:30b.', 'ai-plugin-builder' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
