<?php
/**
 * Settings screen: the builder service connection, and the list of AI platforms (add / edit / delete /
 * make default / test connection).
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers plugin settings, the AI platform handlers, and renders the settings submenu page.
 */
class Aiwpb_Settings {

	const GROUP = 'aiwpb_settings';
	const PAGE  = 'ai-plugin-builder-settings';

	/**
	 * Wire up hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_post_aiwpb_save_platform', array( $this, 'handle_save' ) );
		add_action( 'admin_post_aiwpb_delete_platform', array( $this, 'handle_delete' ) );
		add_action( 'admin_post_aiwpb_default_platform', array( $this, 'handle_default' ) );
	}

	/**
	 * Register the service-connection options with sanitizing callbacks.
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
			self::PAGE,
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue the settings-page script (platform form + Test buttons).
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook ) {
		if ( false === strpos( (string) $hook, self::PAGE ) ) {
			return;
		}
		wp_enqueue_style( 'aiwpb-admin', AIWPB_URL . 'assets/admin.css', array(), AIWPB_VERSION );
		wp_enqueue_script( 'aiwpb-settings', AIWPB_URL . 'assets/settings.js', array(), AIWPB_VERSION, true );
		$types = array();
		foreach ( Aiwpb_Platforms::types() as $key => $t ) {
			$types[ $key ] = array(
				'baseUrl'  => (string) $t['base_url'],
				'needsKey' => ! empty( $t['needs_key'] ),
				'protocol' => (string) $t['protocol'],
			);
		}
		wp_localize_script(
			'aiwpb-settings',
			'AIWPB_SETTINGS',
			array(
				'root'    => esc_url_raw( rest_url( 'aiwpb/v1' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'types'   => $types,
				'testing' => __( 'Testing…', 'ai-plugin-builder' ),
				'models'  => __( 'Available models:', 'ai-plugin-builder' ),
			)
		);
	}

	/**
	 * URL of this settings page, with optional query args.
	 *
	 * @param array $args Extra query args.
	 * @return string
	 */
	private function page_url( $args = array() ) {
		return add_query_arg( array_merge( array( 'page' => self::PAGE ), $args ), admin_url( 'admin.php' ) );
	}

	/**
	 * Capability guard shared by the platform handlers (each also verifies its own nonce).
	 *
	 * @return void
	 */
	private function require_cap() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'ai-plugin-builder' ), '', array( 'response' => 403 ) );
		}
	}

	/**
	 * Add or update a platform (admin-post).
	 *
	 * @return void
	 */
	public function handle_save() {
		$this->require_cap();
		check_admin_referer( 'aiwpb_save_platform' );
		$input = array();
		foreach ( array( 'id', 'name', 'type', 'base_url', 'api_key', 'model', 'clear_key' ) as $field ) {
			$input[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
		}
		$result = Aiwpb_Platforms::save( $input );
		if ( is_wp_error( $result ) ) {
			set_transient( 'aiwpb_platform_error_' . get_current_user_id(), $result->get_error_message(), 60 );
			$back = '' !== $input['id'] ? array( 'edit' => sanitize_key( $input['id'] ) ) : array( 'add' => 1 );
			wp_safe_redirect( $this->page_url( $back + array( 'aiwpb_notice' => 'error' ) ) . '#aiwpb-platform-form' );
			exit;
		}
		wp_safe_redirect( $this->page_url( array( 'aiwpb_notice' => 'saved' ) ) . '#aiwpb-platforms' );
		exit;
	}

	/**
	 * Delete a platform (admin-post).
	 *
	 * @return void
	 */
	public function handle_delete() {
		$this->require_cap();
		check_admin_referer( 'aiwpb_delete_platform' );
		$id = isset( $_GET['id'] ) ? sanitize_key( wp_unslash( $_GET['id'] ) ) : '';
		Aiwpb_Platforms::delete( $id );
		wp_safe_redirect( $this->page_url( array( 'aiwpb_notice' => 'deleted' ) ) . '#aiwpb-platforms' );
		exit;
	}

	/**
	 * Make a platform the default (admin-post).
	 *
	 * @return void
	 */
	public function handle_default() {
		$this->require_cap();
		check_admin_referer( 'aiwpb_default_platform' );
		$id = isset( $_GET['id'] ) ? sanitize_key( wp_unslash( $_GET['id'] ) ) : '';
		Aiwpb_Platforms::set_default( $id );
		wp_safe_redirect( $this->page_url( array( 'aiwpb_notice' => 'default' ) ) . '#aiwpb-platforms' );
		exit;
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap aiwpb-settings">
			<h1><?php echo esc_html__( 'AI Plugin Builder — Settings', 'ai-plugin-builder' ); ?></h1>
			<?php
			$this->render_notice();
			$this->render_service();
			$this->render_platforms();
			$this->render_platform_form();
			?>
		</div>
		<?php
	}

	/**
	 * Result notice after a platform action.
	 *
	 * @return void
	 */
	private function render_notice() {
		// Read-only display flag set by our own redirects; no state change.
		$notice = isset( $_GET['aiwpb_notice'] ) ? sanitize_key( wp_unslash( $_GET['aiwpb_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$texts  = array(
			'saved'   => __( 'AI platform saved.', 'ai-plugin-builder' ),
			'deleted' => __( 'AI platform deleted.', 'ai-plugin-builder' ),
			'default' => __( 'Default AI platform updated.', 'ai-plugin-builder' ),
		);
		if ( isset( $texts[ $notice ] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $texts[ $notice ] ) . '</p></div>';
		} elseif ( 'error' === $notice ) {
			$key = 'aiwpb_platform_error_' . get_current_user_id();
			$msg = (string) get_transient( $key );
			delete_transient( $key );
			if ( '' !== $msg ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $msg ) . '</p></div>';
			}
		}
	}

	/**
	 * Section 1: the builder service connection (options.php form).
	 *
	 * @return void
	 */
	private function render_service() {
		?>
		<h2><?php echo esc_html__( 'Builder service', 'ai-plugin-builder' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( self::GROUP ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="aiwpb_backend_url"><?php echo esc_html__( 'Builder service URL', 'ai-plugin-builder' ); ?></label></th>
					<td>
						<input name="aiwpb_backend_url" id="aiwpb_backend_url" type="url" class="regular-text" placeholder="http://localhost:8787"
							value="<?php echo esc_attr( get_option( 'aiwpb_backend_url', '' ) ); ?>" />
						<p class="description"><?php echo esc_html__( 'Base URL of the AI WP Plugin Builder service (the machine running `npm run serve`).', 'ai-plugin-builder' ); ?></p>
						<div class="aiwpb-service-help">
							<strong><?php echo esc_html__( 'Starting / restarting the builder service', 'ai-plugin-builder' ); ?></strong>
							<p><?php echo esc_html__( 'If Generate or Test fails to connect, the service is not running. From the AI WP Plugin Builder project directory (on the machine with Docker + Node), run:', 'ai-plugin-builder' ); ?></p>
							<p><code>AIWPB_API_KEY=&lt;<?php echo esc_html__( 'the API key below', 'ai-plugin-builder' ); ?>&gt; npm run serve</code></p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="aiwpb_api_key"><?php echo esc_html__( 'Service API key', 'ai-plugin-builder' ); ?></label></th>
					<td>
						<input name="aiwpb_api_key" id="aiwpb_api_key" type="password" autocomplete="off" class="regular-text"
							value="<?php echo esc_attr( get_option( 'aiwpb_api_key', '' ) ); ?>" />
						<p class="description"><?php echo esc_html__( 'Must match AIWPB_API_KEY on the service. This is the service password, not an AI key — AI keys go in the list below.', 'ai-plugin-builder' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save service settings', 'ai-plugin-builder' ) ); ?>
		</form>
		<?php
	}

	/**
	 * Section 2: the list of AI platforms.
	 *
	 * @return void
	 */
	private function render_platforms() {
		$all        = Aiwpb_Platforms::all();
		$default_id = Aiwpb_Platforms::default_id();
		$admin_post = admin_url( 'admin-post.php' );
		?>
		<hr />
		<h2 id="aiwpb-platforms">
			<?php echo esc_html__( 'AI platforms', 'ai-plugin-builder' ); ?>
			<a href="<?php echo esc_url( $this->page_url( array( 'add' => 1 ) ) . '#aiwpb-platform-form' ); ?>" class="page-title-action"><?php echo esc_html__( 'Add AI platform', 'ai-plugin-builder' ); ?></a>
		</h2>
		<p class="description"><?php echo esc_html__( 'The AIs you can chat and build with. The default is preselected on the builder screen. Tests run from the builder service, so a pass means builds can reach that AI.', 'ai-plugin-builder' ); ?></p>
		<table class="widefat striped aiwpb-platforms">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'Name', 'ai-plugin-builder' ); ?></th>
					<th><?php echo esc_html__( 'Type', 'ai-plugin-builder' ); ?></th>
					<th><?php echo esc_html__( 'Model', 'ai-plugin-builder' ); ?></th>
					<th><?php echo esc_html__( 'Endpoint', 'ai-plugin-builder' ); ?></th>
					<th><?php echo esc_html__( 'API key', 'ai-plugin-builder' ); ?></th>
					<th><?php echo esc_html__( 'Connection', 'ai-plugin-builder' ); ?></th>
					<th><?php echo esc_html__( 'Actions', 'ai-plugin-builder' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $all ) ) : ?>
				<tr><td colspan="7"><?php echo esc_html__( 'No AI platforms yet. Add one to start building.', 'ai-plugin-builder' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $all as $id => $p ) : ?>
				<?php
				$masked   = Aiwpb_Platforms::masked_key( $p );
				$last     = is_array( $p['last_test'] ) ? $p['last_test'] : null;
				$del_url  = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'aiwpb_delete_platform',
							'id'     => $id,
						),
						$admin_post
					),
					'aiwpb_delete_platform'
				);
				$def_url  = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'aiwpb_default_platform',
							'id'     => $id,
						),
						$admin_post
					),
					'aiwpb_default_platform'
				);
				$edit_url = $this->page_url( array( 'edit' => $id ) ) . '#aiwpb-platform-form';
				?>
				<tr>
					<td>
						<strong><?php echo esc_html( $p['name'] ); ?></strong>
						<?php if ( $id === $default_id ) : ?>
							<span class="aiwpb-badge aiwpb-badge--done"><?php echo esc_html__( 'default', 'ai-plugin-builder' ); ?></span>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html( Aiwpb_Platforms::type_label( $p ) ); ?></td>
					<td><code><?php echo esc_html( '' !== $p['model'] ? $p['model'] : '—' ); ?></code></td>
					<td><code><?php echo esc_html( $p['base_url'] ); ?></code></td>
					<td>
						<?php
						if ( '' !== $masked ) {
							echo esc_html( $masked );
						} elseif ( 'anthropic' === $p['protocol'] ) {
							echo esc_html__( 'service env', 'ai-plugin-builder' );
						} else {
							echo esc_html__( 'none', 'ai-plugin-builder' );
						}
						?>
					</td>
					<td class="aiwpb-test-cell" id="aiwpb-test-<?php echo esc_attr( $id ); ?>">
						<?php if ( $last ) : ?>
							<span class="aiwpb-test <?php echo esc_attr( $last['ok'] ? 'aiwpb-test--ok' : 'aiwpb-test--fail' ); ?>">
								<?php echo esc_html( $last['ok'] ? '✔ ' : '✖ ' ); ?><?php echo esc_html( $last['message'] ); ?>
							</span>
							<br /><small>
							<?php
							/* translators: %s: human-readable time difference, e.g. "5 mins" */
							echo esc_html( sprintf( __( 'tested %s ago', 'ai-plugin-builder' ), human_time_diff( (int) $last['time'] ) ) );
							?>
							</small>
						<?php else : ?>
							<span class="aiwpb-test"><?php echo esc_html__( 'Not tested yet', 'ai-plugin-builder' ); ?></span>
						<?php endif; ?>
					</td>
					<td class="aiwpb-actions">
						<button type="button" class="button button-small aiwpb-test-btn" data-id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html__( 'Test', 'ai-plugin-builder' ); ?></button>
						<a class="button button-small" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html__( 'Edit', 'ai-plugin-builder' ); ?></a>
						<?php if ( $id !== $default_id ) : ?>
							<a class="button button-small" href="<?php echo esc_url( $def_url ); ?>"><?php echo esc_html__( 'Make default', 'ai-plugin-builder' ); ?></a>
						<?php endif; ?>
						<a class="button button-small button-link-delete aiwpb-delete" href="<?php echo esc_url( $del_url ); ?>"><?php echo esc_html__( 'Delete', 'ai-plugin-builder' ); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Section 3: add / edit form (shown when ?add=1 or ?edit=<id>).
	 *
	 * @return void
	 */
	private function render_platform_form() {
		// Read-only view switches; the form itself is nonce-protected on submit.
		$edit_id = isset( $_GET['edit'] ) ? sanitize_key( wp_unslash( $_GET['edit'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$adding  = isset( $_GET['add'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$p       = '' !== $edit_id ? Aiwpb_Platforms::get( $edit_id ) : null;
		if ( ! $p && ! $adding ) {
			return;
		}
		$types = Aiwpb_Platforms::types();
		$p     = $p ? $p : array(
			'id'       => '',
			'name'     => '',
			'type'     => 'anthropic',
			'base_url' => '',
			'api_key'  => '',
			'model'    => '',
		);
		?>
		<hr />
		<h2 id="aiwpb-platform-form"><?php echo esc_html( '' !== $p['id'] ? __( 'Edit AI platform', 'ai-plugin-builder' ) : __( 'Add AI platform', 'ai-plugin-builder' ) ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="aiwpb_save_platform" />
			<input type="hidden" name="id" value="<?php echo esc_attr( $p['id'] ); ?>" />
			<?php wp_nonce_field( 'aiwpb_save_platform' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="aiwpb-p-type"><?php echo esc_html__( 'Type', 'ai-plugin-builder' ); ?></label></th>
					<td>
						<select name="type" id="aiwpb-p-type">
							<?php foreach ( $types as $key => $t ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $p['type'], $key ); ?>><?php echo esc_html( $t['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php echo esc_html__( 'A new platform not listed? Most are OpenAI-compatible — choose "Other (OpenAI-compatible)" and enter its base URL.', 'ai-plugin-builder' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="aiwpb-p-name"><?php echo esc_html__( 'Name', 'ai-plugin-builder' ); ?></label></th>
					<td><input name="name" id="aiwpb-p-name" type="text" class="regular-text" value="<?php echo esc_attr( $p['name'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g. Ollama on thing2', 'ai-plugin-builder' ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="aiwpb-p-base"><?php echo esc_html__( 'Base URL', 'ai-plugin-builder' ); ?></label></th>
					<td>
						<input name="base_url" id="aiwpb-p-base" type="url" class="regular-text" value="<?php echo esc_attr( $p['base_url'] ); ?>" />
						<p class="description"><?php echo esc_html__( 'Leave blank to use the type\'s standard endpoint. For Ollama, "127.0.0.1" means the machine running the builder service.', 'ai-plugin-builder' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="aiwpb-p-model"><?php echo esc_html__( 'Model', 'ai-plugin-builder' ); ?></label></th>
					<td>
						<input name="model" id="aiwpb-p-model" type="text" class="regular-text" value="<?php echo esc_attr( $p['model'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g. claude-sonnet-5-5, qwen3:30b', 'ai-plugin-builder' ); ?>" />
						<p class="description"><?php echo esc_html__( 'Not sure? Save, then click Test — it lists the models that platform offers.', 'ai-plugin-builder' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="aiwpb-p-key"><?php echo esc_html__( 'API key', 'ai-plugin-builder' ); ?></label></th>
					<td>
						<input name="api_key" id="aiwpb-p-key" type="password" autocomplete="new-password" class="regular-text" value="" />
						<?php if ( '' !== $p['api_key'] ) : ?>
							<p class="description">
								<?php
								/* translators: %s: masked API key, e.g. ••••abcd */
								echo esc_html( sprintf( __( 'Saved key %s — leave blank to keep it.', 'ai-plugin-builder' ), Aiwpb_Platforms::masked_key( $p ) ) );
								?>
								<label><input type="checkbox" name="clear_key" value="1" /> <?php echo esc_html__( 'Remove the saved key', 'ai-plugin-builder' ); ?></label>
							</p>
						<?php endif; ?>
						<p class="description" id="aiwpb-p-key-hint"><?php echo esc_html__( 'Claude: leave blank to use ANTHROPIC_API_KEY on the builder service. Ollama needs no key.', 'ai-plugin-builder' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( '' !== $p['id'] ? __( 'Save AI platform', 'ai-plugin-builder' ) : __( 'Add AI platform', 'ai-plugin-builder' ), 'primary', 'submit', false ); ?>
			<a class="button" href="<?php echo esc_url( $this->page_url() . '#aiwpb-platforms' ); ?>"><?php echo esc_html__( 'Cancel', 'ai-plugin-builder' ); ?></a>
		</form>
		<?php
	}
}
