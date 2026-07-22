<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Qapi
 */

/**
 * Quotes API smoke test.
 */
class Qapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'QAPI_VERSION' ), 'QAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', QAPI_VERSION );
	}

	/**
	 * The quote post type exists.
	 *
	 * @return void
	 */
	public function test_quote_post_type_exists() {
		$this->assertTrue( post_type_exists( 'qapi_quote' ), 'Quote post type should exist.' );
	}

	/**
	 * The REST endpoint /wp/v2/quotes exists.
	 *
	 * @return void
	 */
	public function test_rest_endpoint_exists() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/wp/v2/quotes', $routes, 'REST endpoint /wp/v2/quotes should exist.' );
	}
}
