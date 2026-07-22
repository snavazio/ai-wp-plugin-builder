<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Prcs
 */

/**
 * Press Releases smoke test.
 */
class Prcs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PRCS_VERSION' ), 'PRCS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PRCS_VERSION );
	}

	/**
	 * The press_release post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'press_release' ), 'press_release post type should exist.' );
	}

	/**
	 * The press_releases shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'press_releases' ), 'press_releases shortcode should exist.' );
	}
}
