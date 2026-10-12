<?php
/**
 * My Plugins: the list of plugins built (or imported) through the builder service, with version history and
 * Fix / Change / Refactor / Update actions. Interactivity lives in assets/plugins.js; data comes from the
 * aiwpb/v1/registry REST routes (proxied to the service so its key never reaches the browser).
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the My Plugins submenu and renders its shell.
 */
class Aiwpb_Plugins_Page {

	const PAGE = 'ai-plugin-builder-plugins';

	/**
	 * Wire up hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_page' ), 15 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Add the submenu under the main plugin menu (between the builder and Settings).
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'ai-plugin-builder',
			__( 'My Plugins', 'ai-plugin-builder' ),
			__( 'My Plugins', 'ai-plugin-builder' ),
			'manage_options',
			self::PAGE,
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue assets only on this screen.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook ) {
		if ( false === strpos( (string) $hook, self::PAGE ) ) {
			return;
		}
		wp_enqueue_style( 'aiwpb-admin', AIWPB_URL . 'assets/admin.css', array(), AIWPB_VERSION );
		wp_enqueue_script( 'aiwpb-plugins', AIWPB_URL . 'assets/plugins.js', array(), AIWPB_VERSION, true );

		$edit_platform = Aiwpb_Platforms::resolve( Aiwpb_Platforms::edit_id() );
		$installed     = array();
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		foreach ( get_plugins() as $file => $data ) {
			$dir = dirname( $file );
			if ( '.' !== $dir && 'ai-plugin-builder' !== $dir ) {
				$installed[] = array(
					'slug'    => $dir,
					'name'    => (string) $data['Name'],
					'version' => (string) $data['Version'],
				);
			}
		}

		wp_localize_script(
			'aiwpb-plugins',
			'AIWPB_PLUGINS',
			array(
				'root'          => esc_url_raw( rest_url( 'aiwpb/v1' ) ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'adminPost'     => esc_url_raw( admin_url( 'admin-post.php' ) ),
				'downloadNonce' => wp_create_nonce( 'aiwpb_download_version' ),
				'configured'    => ( new Aiwpb_Client() )->is_configured(),
				'settingsUrl'   => esc_url_raw( admin_url( 'admin.php?page=ai-plugin-builder-settings#aiwpb-edit-ai' ) ),
				'editPlatform'  => $edit_platform ? (string) $edit_platform['id'] : '',
				'editAiLabel'   => $edit_platform ? ( '' !== $edit_platform['model'] ? $edit_platform['name'] . ' — ' . $edit_platform['model'] : $edit_platform['name'] ) : '',
				'installed'     => $installed,
			)
		);
	}

	/**
	 * Render the screen shell. The table and panels are drawn by assets/plugins.js.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap aiwpb-wrap aiwpb-plugins">
			<h1 class="wp-heading-inline"><?php echo esc_html__( 'My Plugins', 'ai-plugin-builder' ); ?></h1>
			<button type="button" id="aiwpb-add-toggle" class="page-title-action"><?php echo esc_html__( 'Add plugin', 'ai-plugin-builder' ); ?></button>
			<hr class="wp-header-end" />
			<p class="description"><?php echo esc_html__( 'Every plugin you build or import, with its versions. Open one to Fix, Change, Refactor or Update it — each edit is re-verified against all the gates and saved as a new version.', 'ai-plugin-builder' ); ?></p>

			<div id="aiwpb-notice" class="aiwpb-notice" aria-live="polite"></div>

			<div id="aiwpb-add" class="aiwpb-card" style="display:none;"></div>

			<div class="aiwpb-toolbar">
				<label class="screen-reader-text" for="aiwpb-search"><?php echo esc_html__( 'Search plugins', 'ai-plugin-builder' ); ?></label>
				<input type="search" id="aiwpb-search" placeholder="<?php echo esc_attr__( 'Search name, client, notes…', 'ai-plugin-builder' ); ?>" />
				<label class="screen-reader-text" for="aiwpb-status-filter"><?php echo esc_html__( 'Filter by status', 'ai-plugin-builder' ); ?></label>
				<select id="aiwpb-status-filter"></select>
				<span id="aiwpb-list-spinner" class="spinner"></span>
			</div>

			<div id="aiwpb-table"></div>
			<div id="aiwpb-detail" class="aiwpb-card" style="display:none;"></div>
		</div>
		<?php
	}
}
