<?php
/**
 * HTTP client for the AI WP Plugin Builder backend service. All backend calls happen server-side so
 * the API key is never exposed to the browser.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thin wrapper around wp_remote_* for talking to the builder service.
 */
class Aiwpb_Client {

	/**
	 * Configured backend base URL (no trailing slash), or ''.
	 *
	 * @return string
	 */
	public function base_url() {
		return untrailingslashit( (string) get_option( 'aiwpb_backend_url', '' ) );
	}

	/**
	 * Configured API key, or ''.
	 *
	 * @return string
	 */
	private function api_key() {
		return (string) get_option( 'aiwpb_api_key', '' );
	}

	/**
	 * True when both a backend URL and key are configured.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return '' !== $this->base_url() && '' !== $this->api_key();
	}

	/**
	 * Common request args with auth header.
	 *
	 * @param array $extra Extra args merged in.
	 * @return array
	 */
	private function args( $extra = array() ) {
		$defaults = array(
			'timeout'   => 20,
			'headers'   => array(
				'X-API-Key'    => $this->api_key(),
				'Content-Type' => 'application/json',
			),
			'sslverify' => apply_filters( 'aiwpb_sslverify', true ),
		);
		return array_merge( $defaults, $extra );
	}

	/**
	 * GET /api/health.
	 *
	 * @return array|WP_Error Decoded body or error.
	 */
	public function health() {
		return $this->decode( wp_remote_get( $this->base_url() . '/api/health', $this->args() ) );
	}

	/**
	 * POST /api/platforms/test — check that the service can reach an AI platform with its key + model.
	 *
	 * @param array $platform Service payload from Aiwpb_Platforms::to_service().
	 * @return array|WP_Error Decoded { ok, message, ms, models? } or error.
	 */
	public function test_platform( $platform ) {
		return $this->decode(
			wp_remote_post(
				$this->base_url() . '/api/platforms/test',
				$this->args(
					array(
						'timeout' => 30,
						'body'    => wp_json_encode( array( 'platform' => $platform ) ),
					)
				)
			)
		);
	}

	/**
	 * POST /api/build.
	 *
	 * @param string $spec     The plugin spec text.
	 * @param array  $platform Service payload from Aiwpb_Platforms::to_service().
	 * @return array|WP_Error
	 */
	public function build( $spec, $platform ) {
		$payload = array(
			'spec'     => $spec,
			'platform' => $platform,
		);
		return $this->decode(
			wp_remote_post(
				$this->base_url() . '/api/build',
				$this->args( array( 'body' => wp_json_encode( $payload ) ) )
			)
		);
	}

	/**
	 * POST /api/chat — one turn of the spec-building conversation.
	 *
	 * @param array  $messages List of { role, content } messages.
	 * @param array  $platform Service payload from Aiwpb_Platforms::to_service().
	 * @param string $mode     '' for a normal turn, 'distill' to consolidate into a final spec.
	 * @return array|WP_Error
	 */
	public function chat( $messages, $platform, $mode = '' ) {
		$payload = array(
			'messages' => array_values( (array) $messages ),
			'platform' => $platform,
		);
		if ( '' !== $mode ) {
			$payload['mode'] = $mode;
		}
		return $this->decode(
			wp_remote_post(
				$this->base_url() . '/api/chat',
				$this->args(
					array(
						'timeout' => 300,
						'body'    => wp_json_encode( $payload ),
					)
				)
			)
		);
	}

	/**
	 * POST /api/ingest — update an existing plugin (sent as base64 zip) to a change request.
	 *
	 * @param string $zip_b64  Base64-encoded plugin .zip.
	 * @param string $spec     Change request (the conversation transcript).
	 * @param array  $platform Service payload from Aiwpb_Platforms::to_service().
	 * @return array|WP_Error
	 */
	public function ingest( $zip_b64, $spec, $platform ) {
		$payload = array(
			'zipB64'   => $zip_b64,
			'spec'     => $spec,
			'platform' => $platform,
		);
		return $this->decode(
			wp_remote_post(
				$this->base_url() . '/api/ingest',
				$this->args(
					array(
						'timeout' => 30,
						'body'    => wp_json_encode( $payload ),
					)
				)
			)
		);
	}

	/**
	 * GET /api/jobs/:id.
	 *
	 * @param string $job_id Job id.
	 * @return array|WP_Error
	 */
	public function job( $job_id ) {
		$url = $this->base_url() . '/api/jobs/' . rawurlencode( $job_id );
		return $this->decode( wp_remote_get( $url, $this->args( array( 'timeout' => 30 ) ) ) );
	}

	/**
	 * GET /api/jobs/:id/zip — returns the raw zip bytes.
	 *
	 * @param string $job_id Job id.
	 * @return string|WP_Error Raw body or error.
	 */
	public function zip( $job_id ) {
		$url      = $this->base_url() . '/api/jobs/' . rawurlencode( $job_id ) . '/zip';
		$response = wp_remote_get( $url, $this->args( array( 'timeout' => 60 ) ) );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'aiwpb_zip', __( 'The service did not return a .zip for this job.', 'ai-plugin-builder' ) );
		}
		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Decode a JSON response, surfacing transport and HTTP errors as WP_Error.
	 *
	 * @param array|WP_Error $response Raw wp_remote response.
	 * @return array|WP_Error
	 */
	private function decode( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $code >= 400 ) {
			$message = is_array( $data ) && isset( $data['error'] ) ? $data['error'] : __( 'The builder service returned an error.', 'ai-plugin-builder' );
			return new WP_Error( 'aiwpb_http_' . $code, (string) $message );
		}
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'aiwpb_decode', __( 'Could not read the response from the builder service.', 'ai-plugin-builder' ) );
		}
		return $data;
	}
}
