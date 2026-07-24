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
	 * POST /api/build.
	 *
	 * @param string $spec   The plugin spec text.
	 * @param string $engine "claude" or "local".
	 * @param string $model  Optional model override.
	 * @return array|WP_Error
	 */
	public function build( $spec, $engine, $model = '' ) {
		$payload = array(
			'spec'   => $spec,
			'engine' => ( 'local' === $engine ) ? 'local' : 'claude',
		);
		if ( '' !== $model ) {
			$payload['model'] = $model;
		}
		return $this->decode(
			wp_remote_post(
				$this->base_url() . '/api/build',
				$this->args( array( 'body' => wp_json_encode( $payload ) ) )
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
