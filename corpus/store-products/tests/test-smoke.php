<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Strp
 */

/**
 * Store Products smoke test.
 */
class Strp_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'STRP_VERSION' ), 'STRP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', STRP_VERSION );
	}

	/**
	 * The product post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'product' ), 'product post type should exist.' );
	}

	/**
	 * The store shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'store' ), 'store shortcode should exist.' );
	}
}
