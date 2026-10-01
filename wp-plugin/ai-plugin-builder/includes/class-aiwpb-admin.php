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
		if ( 'toplevel_page_' . self::PAGE !== $hook ) {
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
				'hasPlatforms'  => ! empty( Aiwpb_Platforms::all() ),
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
			<p class="description"><?php echo esc_html__( 'Chat about the plugin you want — the assistant asks a few questions to get it right. When you\'re happy, click Build plugin: it\'s generated, security-audited against 8 gates, and returned as an install-ready .zip.', 'ai-plugin-builder' ); ?></p>

			<div id="aiwpb-app">
				<div class="aiwpb-mode">
					<button type="button" id="aiwpb-mode-new" class="button button-primary"><?php echo esc_html__( 'Build new plugin', 'ai-plugin-builder' ); ?></button>
					<button type="button" id="aiwpb-mode-update" class="button"><?php echo esc_html__( 'Update existing plugin', 'ai-plugin-builder' ); ?></button>
				</div>
				<div id="aiwpb-source" class="aiwpb-source" style="display:none;">
					<p><strong><?php echo esc_html__( 'Which plugin do you want to update?', 'ai-plugin-builder' ); ?></strong></p>
					<p>
						<label><input type="radio" name="aiwpb-src" value="installed" checked /> <?php echo esc_html__( 'Installed on this site:', 'ai-plugin-builder' ); ?></label>
						<select id="aiwpb-installed"><option value=""><?php echo esc_html__( '— choose a plugin —', 'ai-plugin-builder' ); ?></option></select>
					</p>
					<p>
						<label><input type="radio" name="aiwpb-src" value="upload" /> <?php echo esc_html__( 'Upload a .zip:', 'ai-plugin-builder' ); ?></label>
						<input type="file" id="aiwpb-zip" accept=".zip,application/zip" />
					</p>
					<p class="description"><?php echo esc_html__( 'Then chat about the changes below. Claude is recommended for editing existing code.', 'ai-plugin-builder' ); ?></p>
				</div>
				<div class="aiwpb-chat">
					<div id="aiwpb-messages" class="aiwpb-messages" aria-live="polite"></div>
					<div class="aiwpb-chat-input">
						<input type="text" id="aiwpb-input" class="large-text" placeholder="<?php echo esc_attr__( 'Describe the plugin you want…', 'ai-plugin-builder' ); ?>" autocomplete="off" />
						<button id="aiwpb-send" class="button"><?php echo esc_html__( 'Send', 'ai-plugin-builder' ); ?></button>
						<span id="aiwpb-chat-spinner" class="spinner"></span>
					</div>
					<div class="aiwpb-controls">
						<label for="aiwpb-engine"><?php echo esc_html__( 'AI:', 'ai-plugin-builder' ); ?></label>
						<select id="aiwpb-engine">
							<?php
							$aiwpb_default = Aiwpb_Platforms::default_id();
							foreach ( Aiwpb_Platforms::all() as $aiwpb_id => $aiwpb_p ) :
								$aiwpb_label = '' !== $aiwpb_p['model'] ? $aiwpb_p['name'] . ' — ' . $aiwpb_p['model'] : $aiwpb_p['name'];
								?>
								<option value="<?php echo esc_attr( $aiwpb_id ); ?>" <?php selected( $aiwpb_default, $aiwpb_id ); ?>><?php echo esc_html( $aiwpb_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=ai-plugin-builder-settings#aiwpb-platforms' ) ); ?>"><?php echo esc_html__( 'Manage AIs', 'ai-plugin-builder' ); ?></a>
						<button id="aiwpb-build" class="button button-primary" disabled><?php echo esc_html__( 'Build plugin', 'ai-plugin-builder' ); ?></button>
						<button id="aiwpb-reset" class="button-link"><?php echo esc_html__( 'Start over', 'ai-plugin-builder' ); ?></button>
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
					<p id="aiwpb-result-provides" class="aiwpb-provides"></p>
					<p class="description"><?php echo esc_html__( 'To change it, just tell the assistant what to adjust above and click Build plugin again.', 'ai-plugin-builder' ); ?></p>
					<p>
						<a id="aiwpb-download" class="button" href="#" download><?php echo esc_html__( 'Download .zip', 'ai-plugin-builder' ); ?></a>
						<button id="aiwpb-install" class="button button-primary"><?php echo esc_html__( 'Install & activate', 'ai-plugin-builder' ); ?></button>
						<button id="aiwpb-update-inplace" class="button button-primary" style="display:none;"><?php echo esc_html__( 'Update in place', 'ai-plugin-builder' ); ?></button>
						<button id="aiwpb-rollback" class="button" style="display:none;"><?php echo esc_html__( 'Roll back', 'ai-plugin-builder' ); ?></button>
					</p>
					<div id="aiwpb-install-result"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
