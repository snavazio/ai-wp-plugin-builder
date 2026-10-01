<?php
/**
 * REST + download endpoints used by the admin screen. Every backend call is proxied server-side so the
 * API key stays on the server. All routes require the manage_options capability.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the aiwpb/v1 REST routes and the download handler.
 */
class Aiwpb_Rest {

	const NS = 'aiwpb/v1';

	/**
	 * Wire up hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
		add_action( 'admin_post_aiwpb_download', array( $this, 'download' ) );
	}

	/**
	 * Capability gate shared by all routes.
	 *
	 * @return bool
	 */
	public function can_manage() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function routes() {
		register_rest_route(
			self::NS,
			'/build',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'build' ),
				'args'                => array(
					'spec'     => array(
						'required' => true,
						'type'     => 'string',
					),
					'platform' => array(
						'required' => false,
						'type'     => 'string',
					),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/chat',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'chat' ),
				'args'                => array(
					'messages' => array(
						'required' => true,
						'type'     => 'array',
					),
					'mode'     => array(
						'required' => false,
						'type'     => 'string',
					),
					'platform' => array(
						'required' => false,
						'type'     => 'string',
					),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/jobs/(?P<id>[a-fA-F0-9\-]{36})',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'job' ),
			)
		);
		register_rest_route(
			self::NS,
			'/plugins',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'list_plugins' ),
			)
		);
		register_rest_route(
			self::NS,
			'/ingest',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'ingest' ),
				'args'                => array(
					'spec'     => array(
						'required' => true,
						'type'     => 'string',
					),
					'platform' => array(
						'required' => false,
						'type'     => 'string',
					),
					'source'   => array(
						'required' => false,
						'type'     => 'string',
					),
					'slug'     => array(
						'required' => false,
						'type'     => 'string',
					),
					'zipB64'   => array(
						'required' => false,
						'type'     => 'string',
					),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/update',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'update_in_place' ),
				'args'                => array(
					'jobId' => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/rollback',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'rollback' ),
				'args'                => array(
					'slug' => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/platforms/(?P<id>[a-z0-9]+)/test',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'test_platform' ),
			)
		);
		register_rest_route(
			self::NS,
			'/install',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => array( $this, 'can_manage' ),
				'callback'            => array( $this, 'install' ),
				'args'                => array(
					'jobId' => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);
	}

	/**
	 * POST /build — start a generation job on the service.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function build( WP_REST_Request $request ) {
		$spec = sanitize_textarea_field( (string) $request->get_param( 'spec' ) );
		if ( '' === trim( $spec ) ) {
			return new WP_Error( 'aiwpb_empty', __( 'Please enter a spec.', 'ai-plugin-builder' ), array( 'status' => 400 ) );
		}
		$platform = $this->platform_from( $request );
		if ( is_wp_error( $platform ) ) {
			return $platform;
		}
		$result = ( new Aiwpb_Client() )->build( $spec, $platform );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'aiwpb_build', $result->get_error_message(), array( 'status' => 502 ) );
		}
		return rest_ensure_response( $result );
	}

	/**
	 * POST /chat — one turn of the spec-building conversation (proxied to the service).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function chat( WP_REST_Request $request ) {
		$raw      = (array) $request->get_param( 'messages' );
		$mode     = ( 'distill' === $request->get_param( 'mode' ) ) ? 'distill' : '';
		$messages = array();
		foreach ( $raw as $m ) {
			$role    = ( is_array( $m ) && isset( $m['role'] ) && 'assistant' === $m['role'] ) ? 'assistant' : 'user';
			$content = ( is_array( $m ) && isset( $m['content'] ) ) ? sanitize_textarea_field( (string) $m['content'] ) : '';
			if ( '' !== trim( $content ) ) {
				$messages[] = array(
					'role'    => $role,
					'content' => $content,
				);
			}
		}
		if ( empty( $messages ) ) {
			return new WP_Error( 'aiwpb_chat', __( 'No messages to send.', 'ai-plugin-builder' ), array( 'status' => 400 ) );
		}
		$platform = $this->platform_from( $request );
		if ( is_wp_error( $platform ) ) {
			return $platform;
		}
		$result = ( new Aiwpb_Client() )->chat( $messages, $platform, $mode );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'aiwpb_chat', $result->get_error_message(), array( 'status' => 502 ) );
		}
		return rest_ensure_response( $result );
	}

	/**
	 * The service payload for the platform a request asks for (its `platform` id, or the default).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return array|WP_Error
	 */
	private function platform_from( WP_REST_Request $request ) {
		$p = Aiwpb_Platforms::resolve( sanitize_key( (string) $request->get_param( 'platform' ) ) );
		if ( ! $p ) {
			return new WP_Error( 'aiwpb_platform', __( 'No AI platform is set up. Add one under AI Plugin Builder → Settings.', 'ai-plugin-builder' ), array( 'status' => 400 ) );
		}
		return Aiwpb_Platforms::to_service( $p );
	}

	/**
	 * POST /platforms/:id/test — ask the builder service to test one saved platform, and record the result.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function test_platform( WP_REST_Request $request ) {
		$id = sanitize_key( (string) $request->get_param( 'id' ) );
		$p  = Aiwpb_Platforms::get( $id );
		if ( ! $p ) {
			return new WP_Error( 'aiwpb_platform', __( 'That AI platform no longer exists.', 'ai-plugin-builder' ), array( 'status' => 404 ) );
		}
		$client = new Aiwpb_Client();
		if ( ! $client->is_configured() ) {
			$result = array(
				'ok'      => false,
				'message' => __( 'Set the builder service URL and API key above first — tests run on the service.', 'ai-plugin-builder' ),
				'ms'      => 0,
			);
		} else {
			$result = $client->test_platform( Aiwpb_Platforms::to_service( $p ) );
			if ( is_wp_error( $result ) ) {
				$result = array(
					'ok'      => false,
					/* translators: %s: error from the builder service connection */
					'message' => sprintf( __( 'Could not reach the builder service: %s', 'ai-plugin-builder' ), $result->get_error_message() ),
					'ms'      => 0,
				);
			}
		}
		Aiwpb_Platforms::record_test( $id, $result );
		return rest_ensure_response(
			array(
				'ok'      => ! empty( $result['ok'] ),
				'message' => isset( $result['message'] ) ? (string) $result['message'] : '',
				'ms'      => isset( $result['ms'] ) ? (int) $result['ms'] : 0,
				'models'  => isset( $result['models'] ) ? array_slice( array_map( 'strval', (array) $result['models'] ), 0, 50 ) : array(),
			)
		);
	}

	/**
	 * GET /plugins — installed plugins (folder-based) available to update.
	 *
	 * @return WP_REST_Response
	 */
	public function list_plugins() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$out = array();
		foreach ( get_plugins() as $file => $data ) {
			$slug = dirname( $file );
			if ( '.' === $slug || 'ai-plugin-builder' === $slug ) {
				continue; // skip single-file plugins and this plugin itself.
			}
			$out[] = array(
				'slug'    => $slug,
				'name'    => $data['Name'],
				'version' => $data['Version'],
			);
		}
		usort(
			$out,
			static function ( $a, $b ) {
				return strcasecmp( $a['name'], $b['name'] );
			}
		);
		return rest_ensure_response( array( 'plugins' => $out ) );
	}

	/**
	 * POST /ingest — update an existing plugin (installed or uploaded) to a change request.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function ingest( WP_REST_Request $request ) {
		$spec   = sanitize_textarea_field( (string) $request->get_param( 'spec' ) );
		$source = ( 'upload' === $request->get_param( 'source' ) ) ? 'upload' : 'installed';
		if ( '' === trim( $spec ) ) {
			return new WP_Error( 'aiwpb_ingest', __( 'Describe the change first.', 'ai-plugin-builder' ), array( 'status' => 400 ) );
		}
		$platform = $this->platform_from( $request );
		if ( is_wp_error( $platform ) ) {
			return $platform;
		}

		if ( 'installed' === $source ) {
			$slug    = sanitize_key( (string) $request->get_param( 'slug' ) );
			$zip_b64 = '' !== $slug ? $this->zip_installed_plugin( $slug ) : new WP_Error( 'aiwpb_ingest', __( 'Choose a plugin to update.', 'ai-plugin-builder' ) );
		} else {
			$raw     = (string) $request->get_param( 'zipB64' );
			$zip_b64 = '' !== $raw ? $raw : new WP_Error( 'aiwpb_ingest', __( 'Upload a plugin .zip.', 'ai-plugin-builder' ) );
		}
		if ( is_wp_error( $zip_b64 ) ) {
			return new WP_Error( 'aiwpb_ingest', $zip_b64->get_error_message(), array( 'status' => 400 ) );
		}

		$result = ( new Aiwpb_Client() )->ingest( $zip_b64, $spec, $platform );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'aiwpb_ingest', $result->get_error_message(), array( 'status' => 502 ) );
		}
		return rest_ensure_response( $result );
	}

	/**
	 * POST /update — install the updated plugin from a finished job in place, with a rollback backup.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_in_place( WP_REST_Request $request ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$job_id = sanitize_text_field( (string) $request->get_param( 'jobId' ) );
		$client = new Aiwpb_Client();
		$job    = $client->job( $job_id );
		$slug   = ( ! is_wp_error( $job ) && isset( $job['artifact']['slug'] ) ) ? sanitize_key( $job['artifact']['slug'] ) : '';
		$bytes  = $client->zip( $job_id );
		if ( is_wp_error( $bytes ) ) {
			return new WP_Error( 'aiwpb_update', $bytes->get_error_message(), array( 'status' => 502 ) );
		}

		$was_installed = '' !== $slug && is_dir( trailingslashit( WP_PLUGIN_DIR ) . $slug );
		$backup        = false;
		if ( $was_installed ) {
			$backup = ! is_wp_error( $this->backup_plugin( $slug ) );
		}

		$plugin_file = $this->install_zip_bytes( $bytes );
		if ( is_wp_error( $plugin_file ) ) {
			if ( $backup ) {
				$this->restore_backup( $slug );
			}
			return new WP_Error( 'aiwpb_update', $plugin_file->get_error_message(), array( 'status' => 500 ) );
		}
		$activation = activate_plugin( $plugin_file );
		if ( is_wp_error( $activation ) ) {
			if ( $backup ) {
				$this->restore_backup( $slug );
			}
			return new WP_Error(
				'aiwpb_update',
				sprintf(
					/* translators: %s: activation error */
					__( 'Update installed but failed to activate (%s). Rolled back to the previous version.', 'ai-plugin-builder' ),
					$activation->get_error_message()
				),
				array( 'status' => 500 )
			);
		}
		return rest_ensure_response(
			array(
				'updated'     => true,
				'activated'   => true,
				'slug'        => $slug,
				'canRollback' => $backup,
				'message'     => __( 'Plugin updated in place and activated.', 'ai-plugin-builder' ),
			)
		);
	}

	/**
	 * POST /rollback — restore the pre-update backup of a plugin.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function rollback( WP_REST_Request $request ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$slug   = sanitize_key( (string) $request->get_param( 'slug' ) );
		$result = $this->restore_backup( $slug );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'aiwpb_rollback', $result->get_error_message(), array( 'status' => 500 ) );
		}
		return rest_ensure_response(
			array(
				'rolledBack' => true,
				'message'    => __( 'Rolled back to the previous version.', 'ai-plugin-builder' ),
			)
		);
	}

	/**
	 * Zip an installed plugin folder and return it base64-encoded.
	 *
	 * @param string $slug Plugin folder slug.
	 * @return string|WP_Error
	 */
	private function zip_installed_plugin( $slug ) {
		$dir = trailingslashit( WP_PLUGIN_DIR ) . $slug;
		if ( ! is_dir( $dir ) ) {
			return new WP_Error( 'aiwpb_notfound', __( 'That plugin was not found.', 'ai-plugin-builder' ) );
		}
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'aiwpb_zip', __( 'ZipArchive is required on the server.', 'ai-plugin-builder' ) );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();
		$tmp = wp_tempnam( $slug . '.zip' );
		$zip = new ZipArchive();
		if ( ! $tmp || true !== $zip->open( $tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
			return new WP_Error( 'aiwpb_zip', __( 'Could not create the archive.', 'ai-plugin-builder' ) );
		}
		$items = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
		foreach ( $items as $file ) {
			$rel = $slug . '/' . str_replace( '\\', '/', substr( $file->getPathname(), strlen( $dir ) + 1 ) );
			$zip->addFile( $file->getPathname(), $rel );
		}
		$zip->close();
		$bytes = $wp_filesystem->get_contents( $tmp );
		$wp_filesystem->delete( $tmp );
		if ( false === $bytes ) {
			return new WP_Error( 'aiwpb_zip', __( 'Could not read the archive.', 'ai-plugin-builder' ) );
		}
		return base64_encode( $bytes );
	}

	/**
	 * Write a .zip to a temp file and install it (overwriting), returning the main plugin file.
	 *
	 * @param string $bytes Raw .zip bytes.
	 * @return string|WP_Error Plugin file path or error.
	 */
	private function install_zip_bytes( $bytes ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		global $wp_filesystem;
		if ( ! WP_Filesystem() ) {
			return new WP_Error( 'aiwpb_fs', __( 'Could not initialize the filesystem.', 'ai-plugin-builder' ) );
		}
		$tmp = wp_tempnam( 'aiwpb-plugin.zip' );
		if ( ! $tmp || ! $wp_filesystem->put_contents( $tmp, $bytes ) ) {
			return new WP_Error( 'aiwpb_write', __( 'Could not write the plugin package.', 'ai-plugin-builder' ) );
		}
		$skin     = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$outcome  = $upgrader->install( $tmp, array( 'overwrite_package' => true ) );
		$wp_filesystem->delete( $tmp );
		if ( is_wp_error( $outcome ) ) {
			return $outcome;
		}
		if ( true !== $outcome ) {
			return new WP_Error( 'aiwpb_install', __( 'Installation failed.', 'ai-plugin-builder' ), array( 'messages' => $skin->get_upgrade_messages() ) );
		}
		return $upgrader->plugin_info();
	}

	/**
	 * Back up the current copy of a plugin (for rollback), keeping one backup per slug.
	 *
	 * @param string $slug Plugin slug.
	 * @return string|WP_Error Backup path or error.
	 */
	private function backup_plugin( $slug ) {
		$b64 = $this->zip_installed_plugin( $slug );
		if ( is_wp_error( $b64 ) ) {
			return $b64;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();
		$uploads = wp_upload_dir();
		$dir     = trailingslashit( $uploads['basedir'] ) . 'aiwpb-backups';
		if ( ! $wp_filesystem->is_dir( $dir ) ) {
			$wp_filesystem->mkdir( $dir );
		}
		$path = trailingslashit( $dir ) . $slug . '.zip';
		if ( ! $wp_filesystem->put_contents( $path, base64_decode( $b64 ) ) ) {
			return new WP_Error( 'aiwpb_backup', __( 'Could not write the backup.', 'ai-plugin-builder' ) );
		}
		update_option( 'aiwpb_backup_' . $slug, $path, false );
		return $path;
	}

	/**
	 * Restore a plugin from its backup and reactivate it.
	 *
	 * @param string $slug Plugin slug.
	 * @return string|WP_Error Plugin file path or error.
	 */
	private function restore_backup( $slug ) {
		$path = (string) get_option( 'aiwpb_backup_' . $slug, '' );
		if ( '' === $path ) {
			return new WP_Error( 'aiwpb_rollback', __( 'No backup is available for this plugin.', 'ai-plugin-builder' ) );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		global $wp_filesystem;
		WP_Filesystem();
		$bytes = $wp_filesystem->get_contents( $path );
		if ( false === $bytes ) {
			return new WP_Error( 'aiwpb_rollback', __( 'The backup file is missing.', 'ai-plugin-builder' ) );
		}
		$plugin_file = $this->install_zip_bytes( $bytes );
		if ( is_wp_error( $plugin_file ) ) {
			return $plugin_file;
		}
		activate_plugin( $plugin_file );
		return $plugin_file;
	}

	/**
	 * GET /jobs/:id — proxy the job status.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function job( WP_REST_Request $request ) {
		$id     = sanitize_text_field( (string) $request->get_param( 'id' ) );
		$result = ( new Aiwpb_Client() )->job( $id );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'aiwpb_job', $result->get_error_message(), array( 'status' => 502 ) );
		}
		return rest_ensure_response( $result );
	}

	/**
	 * POST /install — fetch the built .zip from the service and install it on this site.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function install( WP_REST_Request $request ) {
		$job_id = sanitize_text_field( (string) $request->get_param( 'jobId' ) );
		$client = new Aiwpb_Client();
		$bytes  = $client->zip( $job_id );
		if ( is_wp_error( $bytes ) ) {
			return new WP_Error( 'aiwpb_zip', $bytes->get_error_message(), array( 'status' => 502 ) );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugin_file = $this->install_zip_bytes( $bytes );
		if ( is_wp_error( $plugin_file ) ) {
			return new WP_Error( 'aiwpb_install', $plugin_file->get_error_message(), array( 'status' => 500 ) );
		}

		// Auto-activate the freshly installed plugin. Activation hooks run; report if the plugin fatals.
		$activated      = false;
		$activate_error = '';
		if ( $plugin_file ) {
			$activation = activate_plugin( $plugin_file );
			if ( is_wp_error( $activation ) ) {
				$activate_error = $activation->get_error_message();
			} else {
				$activated = true;
			}
		}

		$message = $activated
			? __( 'Plugin installed and activated.', 'ai-plugin-builder' )
			: sprintf(
				/* translators: %s: activation error message */
				__( 'Plugin installed, but activation failed: %s', 'ai-plugin-builder' ),
				'' !== $activate_error ? $activate_error : __( 'unknown error', 'ai-plugin-builder' )
			);

		return rest_ensure_response(
			array(
				'installed' => true,
				'activated' => $activated,
				'plugin'    => $plugin_file,
				'message'   => $message,
			)
		);
	}

	/**
	 * Download handler (admin-post): streams the built .zip to the browser.
	 *
	 * @return void
	 */
	public function download() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'ai-plugin-builder' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'aiwpb_download' );
		$job_id = isset( $_GET['job'] ) ? sanitize_text_field( wp_unslash( $_GET['job'] ) ) : '';
		if ( '' === $job_id ) {
			wp_die( esc_html__( 'Missing job id.', 'ai-plugin-builder' ) );
		}
		$client = new Aiwpb_Client();
		$bytes  = $client->zip( $job_id );
		if ( is_wp_error( $bytes ) ) {
			wp_die( esc_html( $bytes->get_error_message() ) );
		}
		$job  = $client->job( $job_id );
		$name = ( ! is_wp_error( $job ) && isset( $job['artifact']['slug'] ) )
			? sanitize_file_name( $job['artifact']['slug'] . '-' . ( isset( $job['artifact']['version'] ) ? $job['artifact']['version'] : '1.0.0' ) . '.zip' )
			: 'plugin.zip';

		nocache_headers();
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="' . $name . '"' );
		header( 'Content-Length: ' . strlen( $bytes ) );
		echo $bytes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- binary zip payload.
		exit;
	}
}
