<?php
/**
 * Main admin screen: enter a spec, generate, watch progress, download or install the verified plugin.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the top-level menu and renders the builder screen.
 */
class Aiwpb_Admin {

	const PAGE = 'ai-plugin-builder';

	/**
	 * Wire up hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Add the top-level menu + main page.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_menu_page(
			__( 'AI Plugin Builder', 'ai-plugin-builder' ),
			__( 'AI Plugin Builder', 'ai-plugin-builder' ),
			'manage_options',
			self::PAGE,
			array( $this, 'render' ),
			'dashicons-hammer',
			62
		);
	}

	/**
	 * Enqueue assets only on this plugin's screens.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook ) {
		if ( false === strpos( (string) $hook, self::PAGE ) ) {
			return;
		}
		wp_enqueue_style( 'aiwpb-admin', AIWPB_URL . 'assets/admin.css', array(), AIWPB_VERSION );
		wp_enqueue_script( 'aiwpb-admin', AIWPB_URL . 'assets/admin.js', array( 'wp-api-fetch' ), AIWPB_VERSION, true );
		wp_localize_script(
			'aiwpb-admin',
			'AIWPB',
			array(
				'root'          => esc_url_raw( rest_url( 'aiwpb/v1' ) ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'configured'    => ( new Aiwpb_Client() )->is_configured(),
				'defaultEngine' => (string) get_option( 'aiwpb_default_engine', 'claude' ),
				'localModel'    => (string) get_option( 'aiwpb_local_model', 'qwen3:30b' ),
				'settingsUrl'   => esc_url_raw( admin_url( 'admin.php?page=ai-plugin-builder-settings' ) ),
				'adminPost'     => esc_url_raw( admin_url( 'admin-post.php' ) ),
				'downloadNonce' => wp_create_nonce( 'aiwpb_download' ),
			)
		);
	}

	/**
	 * Render the builder screen. Interactivity lives in assets/admin.js.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap aiwpb-wrap">
			<h1><?php echo esc_html__( 'AI Plugin Builder', 'ai-plugin-builder' ); ?></h1>
			<p class="description"><?php echo esc_html__( 'Describe the plugin you want in plain English. It is generated, security-audited against 8 gates, and returned as an install-ready .zip.', 'ai-plugin-builder' ); ?></p>

			<div id="aiwpb-app">
				<div class="aiwpb-form">
					<label for="aiwpb-spec"><strong><?php echo esc_html__( 'Plugin spec', 'ai-plugin-builder' ); ?></strong></label>
					<textarea id="aiwpb-spec" rows="10" class="large-text code" placeholder="<?php echo esc_attr__( 'e.g. A Testimonials custom post type with a 1-5 star rating and a shortcode that lists recent testimonials.', 'ai-plugin-builder' ); ?>"></textarea>

					<div class="aiwpb-controls">
						<label for="aiwpb-engine"><?php echo esc_html__( 'Engine:', 'ai-plugin-builder' ); ?></label>
						<select id="aiwpb-engine">
							<option value="claude"><?php echo esc_html__( 'Claude (Agent SDK)', 'ai-plugin-builder' ); ?></option>
							<option value="local"><?php echo esc_html__( 'Local (Ollama, $0)', 'ai-plugin-builder' ); ?></option>
						</select>
						<button id="aiwpb-generate" class="button button-primary"><?php echo esc_html__( 'Generate Plugin', 'ai-plugin-builder' ); ?></button>
						<span id="aiwpb-spinner" class="spinner"></span>
					</div>
					<p id="aiwpb-config-warning" class="notice notice-warning" style="display:none;padding:8px 12px;">
						<?php
						printf(
							/* translators: %s: settings page URL */
							wp_kses( __( 'The builder service is not configured yet. <a href="%s">Set the service URL and API key</a>.', 'ai-plugin-builder' ), array( 'a' => array( 'href' => array() ) ) ),
							esc_url( admin_url( 'admin.php?page=ai-plugin-builder-settings' ) )
						);
						?>
					</p>
				</div>

				<div id="aiwpb-status" class="aiwpb-status" style="display:none;">
					<h2><?php echo esc_html__( 'Progress', 'ai-plugin-builder' ); ?> <span id="aiwpb-state" class="aiwpb-badge"></span></h2>
					<pre id="aiwpb-log" class="aiwpb-log" aria-live="polite"></pre>
				</div>

				<div id="aiwpb-result" class="aiwpb-result" style="display:none;">
					<h2><?php echo esc_html__( 'Result', 'ai-plugin-builder' ); ?></h2>
					<p id="aiwpb-result-summary"></p>
					<p>
						<a id="aiwpb-download" class="button" href="#" download><?php echo esc_html__( 'Download .zip', 'ai-plugin-builder' ); ?></a>
						<button id="aiwpb-install" class="button button-primary"><?php echo esc_html__( 'Install on this site', 'ai-plugin-builder' ); ?></button>
					</p>
					<div id="aiwpb-install-result"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
