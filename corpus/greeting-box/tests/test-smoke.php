<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Gbox
 */

/**
 * Greeting Box smoke test.
 */
class Gbox_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'GBOX_VERSION' ), 'GBOX_VERSION should be defined.' );
		$this->assertSame( '1.0.0', GBOX_VERSION );
	}

	/**
	 * The greeting_box shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'greeting_box' ) );
	}

	/**
	 * The greeting_box shortcode function exists.
	 *
	 * @return void
	 */
	public function test_shortcode_function_exists() {
		$this->assertTrue( function_exists( 'gbox_greeting_box_shortcode' ) );
	}
}
