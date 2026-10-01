<?php
/**
 * The list of AI platforms the builder can use: storage, platform types, and the payload sent to the
 * builder service. Keys are stored server-side only and never rendered back into a page.
 *
 * @package AI_Plugin_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CRUD over the saved AI platforms (one option holding an id => record map).
 */
class Aiwpb_Platforms {

	const OPTION         = 'aiwpb_platforms';
	const DEFAULT_OPTION = 'aiwpb_default_platform';

	/**
	 * Wire protocols the builder service understands.
	 *
	 * @var string[]
	 */
	const PROTOCOLS = array( 'anthropic', 'ollama', 'openai', 'gemini' );

	/**
	 * Known platform types. Each maps to one of the service's protocols; most new AI platforms are
	 * OpenAI-compatible, so they work as "Other (OpenAI-compatible)" with their base URL, or can be added
	 * as a preset through the `aiwpb_platform_types` filter.
	 *
	 * @return array<string, array{label: string, protocol: string, base_url: string, needs_key: bool}>
	 */
	public static function types() {
		$types = array(
			'anthropic'  => array(
				'label'     => __( 'Anthropic Claude', 'ai-plugin-builder' ),
				'protocol'  => 'anthropic',
				'base_url'  => 'https://api.anthropic.com',
				'needs_key' => false, // Blank = use ANTHROPIC_API_KEY on the builder service.
			),
			'ollama'     => array(
				'label'     => __( 'Ollama (local or remote)', 'ai-plugin-builder' ),
				'protocol'  => 'ollama',
				'base_url'  => 'http://127.0.0.1:11434',
				'needs_key' => false,
			),
			'openai'     => array(
				'label'     => __( 'OpenAI', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => 'https://api.openai.com/v1',
				'needs_key' => true,
			),
			'gemini'     => array(
				'label'     => __( 'Google Gemini', 'ai-plugin-builder' ),
				'protocol'  => 'gemini',
				'base_url'  => 'https://generativelanguage.googleapis.com/v1beta',
				'needs_key' => true,
			),
			'openrouter' => array(
				'label'     => __( 'OpenRouter', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => 'https://openrouter.ai/api/v1',
				'needs_key' => true,
			),
			'groq'       => array(
				'label'     => __( 'Groq', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => 'https://api.groq.com/openai/v1',
				'needs_key' => true,
			),
			'deepseek'   => array(
				'label'     => __( 'DeepSeek', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => 'https://api.deepseek.com/v1',
				'needs_key' => true,
			),
			'mistral'    => array(
				'label'     => __( 'Mistral', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => 'https://api.mistral.ai/v1',
				'needs_key' => true,
			),
			'xai'        => array(
				'label'     => __( 'xAI Grok', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => 'https://api.x.ai/v1',
				'needs_key' => true,
			),
			'custom'     => array(
				'label'     => __( 'Other (OpenAI-compatible)', 'ai-plugin-builder' ),
				'protocol'  => 'openai',
				'base_url'  => '',
				'needs_key' => false,
			),
		);

		/**
		 * Filter the available AI platform types. Each type needs label, protocol (one of anthropic,
		 * ollama, openai, gemini), base_url, and needs_key.
		 *
		 * @param array $types Platform types keyed by type id.
		 */
		$types = (array) apply_filters( 'aiwpb_platform_types', $types );

		return array_filter(
			$types,
			static function ( $t ) {
				return is_array( $t ) && isset( $t['label'], $t['protocol'] ) && in_array( $t['protocol'], self::PROTOCOLS, true );
			}
		);
	}

	/**
	 * All saved platforms, keyed by id. Seeds the list from the pre-1.1 settings on first use.
	 *
	 * @return array<string, array>
	 */
	public static function all() {
		$stored = get_option( self::OPTION, null );
		if ( ! is_array( $stored ) ) {
			$stored = self::seed();
		}
		return $stored;
	}

	/**
	 * One platform by id, or null.
	 *
	 * @param string $id Platform id.
	 * @return array|null
	 */
	public static function get( $id ) {
		$all = self::all();
		return isset( $all[ $id ] ) ? $all[ $id ] : null;
	}

	/**
	 * The default platform's id ('' if the list is empty).
	 *
	 * @return string
	 */
	public static function default_id() {
		$all = self::all();
		$id  = (string) get_option( self::DEFAULT_OPTION, '' );
		if ( isset( $all[ $id ] ) ) {
			return $id;
		}
		$ids = array_keys( $all );
		return $ids ? (string) $ids[0] : '';
	}

	/**
	 * Resolve a requested id to a platform, falling back to the default.
	 *
	 * @param string $id Requested id ('' = default).
	 * @return array|null
	 */
	public static function resolve( $id ) {
		$p = '' !== $id ? self::get( $id ) : null;
		return $p ? $p : self::get( self::default_id() );
	}

	/**
	 * Mark a platform as the default.
	 *
	 * @param string $id Platform id.
	 * @return void
	 */
	public static function set_default( $id ) {
		if ( self::get( $id ) ) {
			update_option( self::DEFAULT_OPTION, $id, false );
		}
	}

	/**
	 * Create or update a platform from raw (unslashed) form input. Returns the id, or a WP_Error.
	 *
	 * @param array $input Keys: id, name, type, base_url, api_key, clear_key, model.
	 * @return string|WP_Error
	 */
	public static function save( $input ) {
		$types = self::types();
		$type  = isset( $input['type'] ) ? sanitize_key( $input['type'] ) : '';
		if ( ! isset( $types[ $type ] ) ) {
			return new WP_Error( 'aiwpb_platform', __( 'Choose a platform type.', 'ai-plugin-builder' ) );
		}
		$name = isset( $input['name'] ) ? sanitize_text_field( $input['name'] ) : '';
		if ( '' === $name ) {
			$name = $types[ $type ]['label'];
		}
		$base_url = isset( $input['base_url'] ) ? esc_url_raw( trim( $input['base_url'] ), array( 'http', 'https' ) ) : '';
		if ( '' === $base_url ) {
			$base_url = $types[ $type ]['base_url'];
		}
		if ( '' === $base_url ) {
			return new WP_Error( 'aiwpb_platform', __( 'This platform type needs a base URL.', 'ai-plugin-builder' ) );
		}

		$all      = self::all();
		$id       = isset( $input['id'] ) ? sanitize_key( $input['id'] ) : '';
		$existing = ( '' !== $id && isset( $all[ $id ] ) ) ? $all[ $id ] : null;
		if ( ! $existing ) {
			$id = self::new_id();
		}

		// A blank key field keeps the saved key; "clear" removes it.
		$api_key = $existing ? (string) $existing['api_key'] : '';
		$new_key = isset( $input['api_key'] ) ? trim( sanitize_text_field( $input['api_key'] ) ) : '';
		if ( '' !== $new_key ) {
			$api_key = $new_key;
		} elseif ( ! empty( $input['clear_key'] ) ) {
			$api_key = '';
		}
		if ( '' === $api_key && $types[ $type ]['needs_key'] ) {
			return new WP_Error( 'aiwpb_platform', __( 'This platform type needs an API key.', 'ai-plugin-builder' ) );
		}

		$all[ $id ] = array(
			'id'        => $id,
			'name'      => $name,
			'type'      => $type,
			'protocol'  => $types[ $type ]['protocol'],
			'base_url'  => untrailingslashit( $base_url ),
			'api_key'   => $api_key,
			'model'     => isset( $input['model'] ) ? sanitize_text_field( $input['model'] ) : '',
			// Changing the connection invalidates the last test result.
			'last_test' => $existing && self::same_connection( $existing, $base_url, $api_key, $type, $input ) ? $existing['last_test'] : null,
		);
		update_option( self::OPTION, $all, false );
		if ( 1 === count( $all ) ) {
			self::set_default( $id );
		}
		return $id;
	}

	/**
	 * Delete a platform. If it was the default, the first remaining one becomes default.
	 *
	 * @param string $id Platform id.
	 * @return void
	 */
	public static function delete( $id ) {
		$all = self::all();
		unset( $all[ $id ] );
		update_option( self::OPTION, $all, false );
		if ( (string) get_option( self::DEFAULT_OPTION, '' ) === $id ) {
			$ids = array_keys( $all );
			update_option( self::DEFAULT_OPTION, $ids ? (string) $ids[0] : '', false );
		}
	}

	/**
	 * Remember the outcome of a connection test on the platform record.
	 *
	 * @param string $id     Platform id.
	 * @param array  $result Keys: ok, message, ms.
	 * @return void
	 */
	public static function record_test( $id, $result ) {
		$all = self::all();
		if ( ! isset( $all[ $id ] ) ) {
			return;
		}
		$all[ $id ]['last_test'] = array(
			'ok'      => ! empty( $result['ok'] ),
			'message' => isset( $result['message'] ) ? sanitize_text_field( (string) $result['message'] ) : '',
			'ms'      => isset( $result['ms'] ) ? absint( $result['ms'] ) : 0,
			'time'    => time(),
		);
		update_option( self::OPTION, $all, false );
	}

	/**
	 * The platform as the builder service expects it (includes the key — server-side use only).
	 *
	 * @param array $p Platform record.
	 * @return array
	 */
	public static function to_service( $p ) {
		return array(
			'protocol' => $p['protocol'],
			'baseUrl'  => $p['base_url'],
			'apiKey'   => $p['api_key'],
			'model'    => $p['model'],
		);
	}

	/**
	 * Masked key for display: last 4 characters only.
	 *
	 * @param array $p Platform record.
	 * @return string
	 */
	public static function masked_key( $p ) {
		$key = (string) $p['api_key'];
		if ( '' === $key ) {
			return '';
		}
		return '••••' . substr( $key, -4 );
	}

	/**
	 * Human label for a platform's type.
	 *
	 * @param array $p Platform record.
	 * @return string
	 */
	public static function type_label( $p ) {
		$types = self::types();
		return isset( $types[ $p['type'] ] ) ? $types[ $p['type'] ]['label'] : $p['type'];
	}

	/**
	 * Whether a save leaves the connection details unchanged.
	 *
	 * @param array  $old      Existing record.
	 * @param string $base_url New base URL.
	 * @param string $api_key  New key.
	 * @param string $type     New type.
	 * @param array  $input    Raw input (for the model).
	 * @return bool
	 */
	private static function same_connection( $old, $base_url, $api_key, $type, $input ) {
		$model = isset( $input['model'] ) ? sanitize_text_field( $input['model'] ) : '';
		return untrailingslashit( $base_url ) === $old['base_url'] && $api_key === $old['api_key'] && $type === $old['type'] && $model === $old['model'];
	}

	/**
	 * A short random id.
	 *
	 * @return string
	 */
	private static function new_id() {
		return strtolower( wp_generate_password( 10, false, false ) );
	}

	/**
	 * First run on 1.1+: turn the old single-engine settings into two list entries.
	 *
	 * @return array
	 */
	private static function seed() {
		$claude = self::new_id();
		$ollama = self::new_id();
		$all    = array(
			$claude => array(
				'id'        => $claude,
				'name'      => __( 'Claude', 'ai-plugin-builder' ),
				'type'      => 'anthropic',
				'protocol'  => 'anthropic',
				'base_url'  => 'https://api.anthropic.com',
				'api_key'   => '',
				'model'     => 'claude-sonnet-5-5',
				'last_test' => null,
			),
			$ollama => array(
				'id'        => $ollama,
				'name'      => __( 'Local Ollama', 'ai-plugin-builder' ),
				'type'      => 'ollama',
				'protocol'  => 'ollama',
				'base_url'  => 'http://127.0.0.1:11434',
				'api_key'   => '',
				'model'     => (string) get_option( 'aiwpb_local_model', 'qwen3:30b' ),
				'last_test' => null,
			),
		);
		update_option( self::OPTION, $all, false );
		$default = ( 'local' === get_option( 'aiwpb_default_engine', 'claude' ) ) ? $ollama : $claude;
		update_option( self::DEFAULT_OPTION, $default, false );
		return $all;
	}
}
