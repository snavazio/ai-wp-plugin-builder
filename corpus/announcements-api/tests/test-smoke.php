<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Annapi
 */

/**
 * Announcements API smoke test.
 */
class Annapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ANNAPI_VERSION' ), 'ANNAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ANNAPI_VERSION );
	}

	/**
	 * The announcement post type exists.
	 *
	 * @return void
	 */
	public function test_announcement_post_type_exists() {
		$this->assertTrue( post_type_exists( 'annapi_announcement' ), 'Announcement post type should exist.' );
	}

	/**
	 * The REST endpoint for announcements exists.
	 *
	 * @return void
	 */
	public function test_rest_endpoint_exists() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/announcements-api/v1/announcements', $routes, 'REST endpoint for announcements should exist.' );
	}
}
