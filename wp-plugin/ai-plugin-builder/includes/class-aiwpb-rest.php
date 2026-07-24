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
					'spec'   => array(
						'required' => true,
						'type'     => 'string',
					),
					'engine' => array(
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
		$spec   = sanitize_textarea_field( (string) $request->get_param( 'spec' ) );
		$engine = ( 'local' === $request->get_param( 'engine' ) ) ? 'local' : 'claude';
		if ( '' === trim( $spec ) ) {
			return new WP_Error( 'aiwpb_empty', __( 'Please enter a spec.', 'ai-plugin-builder' ), array( 'status' => 400 ) );
		}
		$model  = ( 'local' === $engine ) ? (string) get_option( 'aiwpb_local_model', 'qwen3:30b' ) : '';
		$result = ( new Aiwpb_Client() )->build( $spec, $engine, $model );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'aiwpb_build', $result->get_error_message(), array( 'status' => 502 ) );
		}
		return rest_ensure_response( $result );
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

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		global $wp_filesystem;
		if ( ! WP_Filesystem() ) {
			return new WP_Error( 'aiwpb_fs', __( 'Could not initialize the filesystem.', 'ai-plugin-builder' ), array( 'status' => 500 ) );
		}
		$tmp = wp_tempnam( 'aiwpb-plugin.zip' );
		if ( ! $tmp || ! $wp_filesystem->put_contents( $tmp, $bytes ) ) {
			return new WP_Error( 'aiwpb_write', __( 'Could not write the downloaded plugin.', 'ai-plugin-builder' ), array( 'status' => 500 ) );
		}

		$skin     = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$outcome  = $upgrader->install( $tmp, array( 'overwrite_package' => true ) );
		$wp_filesystem->delete( $tmp );

		if ( is_wp_error( $outcome ) ) {
			return new WP_Error( 'aiwpb_install', $outcome->get_error_message(), array( 'status' => 500 ) );
		}
		if ( true !== $outcome ) {
			return new WP_Error(
				'aiwpb_install',
				__( 'Installation failed.', 'ai-plugin-builder' ),
				array(
					'status'   => 500,
					'messages' => $skin->get_upgrade_messages(),
				)
			);
		}

		// Auto-activate the freshly installed plugin. Activation hooks run; report if the plugin fatals.
		$plugin_file    = $upgrader->plugin_info();
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
