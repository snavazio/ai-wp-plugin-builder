<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Wapi
 */

/**
 * Workshops API smoke test.
 */
class Wapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'WAPI_VERSION' ), 'WAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', WAPI_VERSION );
	}

	/**
	 * The workshop post type exists.
	 *
	 * @return void
	 */
	public function test_workshop_post_type_exists() {
		$this->assertTrue( post_type_exists( 'wapi_workshop' ), 'Workshop post type should exist.' );
	}
}
