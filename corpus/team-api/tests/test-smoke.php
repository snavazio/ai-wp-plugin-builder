<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tapi
 */

/**
 * Team API smoke test.
 */
class Tapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TAPI_VERSION' ), 'TAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TAPI_VERSION );
	}

	/**
	 * The member post type exists.
	 *
	 * @return void
	 */
	public function test_member_post_type_exists() {
		$this->assertTrue( post_type_exists( 'tapi_member' ), 'Team Member post type should exist.' );
	}
}
