<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Strs
 */

/**
 * Stores API smoke test.
 */
class Strs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'STRS_VERSION' ), 'STRS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', STRS_VERSION );
	}

	/**
	 * The store post type exists.
	 *
	 * @return void
	 */
	public function test_store_post_type_exists() {
		$this->assertTrue( post_type_exists( 'strs_store' ), 'Store post type should exist.' );
	}
}
