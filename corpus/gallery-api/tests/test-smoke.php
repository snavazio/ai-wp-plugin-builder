<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Galapi
 */

/**
 * Gallery API smoke test.
 */
class Galapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'GALAPI_VERSION' ), 'GALAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', GALAPI_VERSION );
	}

	/**
	 * The photo post type exists.
	 *
	 * @return void
	 */
	public function test_photo_post_type_exists() {
		$this->assertTrue( post_type_exists( 'galapi_photo' ), 'Photo post type should exist.' );
	}

	/**
	 * The REST endpoint for photos exists.
	 *
	 * @return void
	 */
	public function test_rest_endpoint_exists() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/galapi/v1/photos', $routes, 'REST endpoint for photos should exist.' );
	}
}
