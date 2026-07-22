<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Srvapi
 */

/**
 * Services API smoke test.
 */
class Srvapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SRVAPI_VERSION' ), 'SRVAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SRVAPI_VERSION );
	}

	/**
	 * The service post type exists.
	 *
	 * @return void
	 */
	public function test_service_post_type_exists() {
		$this->assertTrue( post_type_exists( 'srvapi_service' ), 'Service post type should exist.' );
	}
}
