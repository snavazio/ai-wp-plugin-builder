<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Srvs
 */

/**
 * Services smoke test.
 */
class Srvs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SRVS_VERSION' ), 'SRVS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SRVS_VERSION );
	}

	/**
	 * The service post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'service' ), 'service post type should exist.' );
	}

	/**
	 * The services shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'services' ), 'services shortcode should exist.' );
	}
}
