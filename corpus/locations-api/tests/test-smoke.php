<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Locapi
 */

/**
 * Locations API smoke test.
 */
class Locapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'LOCAPI_VERSION' ), 'LOCAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', LOCAPI_VERSION );
	}

	/**
	 * The location post type exists.
	 *
	 * @return void
	 */
	public function test_location_post_type_exists() {
		$this->assertTrue( post_type_exists( 'locapi_location' ), 'Location post type should exist.' );
	}

	/**
	 * The REST endpoint for locations exists.
	 *
	 * @return void
	 */
	public function test_rest_endpoint_exists() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/locapi/v1/locations', $routes, 'REST endpoint for locations should exist.' );
	}
}
