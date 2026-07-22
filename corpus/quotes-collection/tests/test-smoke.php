<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Qcl1
 */

/**
 * Quotes Collection smoke test.
 */
class Qcl1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'QCL1_VERSION' ), 'QCL1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', QCL1_VERSION );
	}

	/**
	 * The quote post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'quote' ), 'quote post type should exist.' );
	}

	/**
	 * The random_quote shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'random_quote' ), 'random_quote shortcode should exist.' );
	}
}
