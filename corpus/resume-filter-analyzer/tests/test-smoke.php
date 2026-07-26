<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rfa
 */

/**
 * Resume Filter Analyzer smoke test.
 */
class Rfa_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RFA_VERSION' ), 'RFA_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RFA_VERSION );
	}

	/**
	 * Test that the rfa_analyzer shortcode is registered.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'rfa_analyzer' ), 'rfa_analyzer shortcode should be registered.' );
	}

	/**
	 * Test that REST API routes are registered.
	 *
	 * @return void
	 */
	public function test_rest_routes_exist() {
		$routes = rest_get_server()->get_routes();
		$this->assertNotNull( $routes['/rfa/v1'], 'rfa/v1 REST namespace should be registered.' );
	}

	/**
	 * Test that rest_api_init hook is registered.
	 *
	 * @return void
	 */
	public function test_rest_api_init_hook() {
		$this->assertNotFalse( has_filter( 'rest_api_init' ), 'rest_api_init hook should be registered.' );
	}
}
