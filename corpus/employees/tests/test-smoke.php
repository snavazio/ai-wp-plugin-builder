<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Empc
 */

/**
 * Employees smoke test.
 */
class Empc_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'EMPC_VERSION' ), 'EMPC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', EMPC_VERSION );
	}

	/**
	 * The employee post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'employee' ), 'employee post type should exist.' );
	}

	/**
	 * The employees shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'employees' ), 'employees shortcode should exist.' );
	}
}
