<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Wcpm
 */

/**
 * Workshop CPT Manager smoke test.
 */
class Wcpm_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'WCPM_VERSION' ), 'WCPM_VERSION should be defined.' );
		$this->assertSame( '1.0.0', WCPM_VERSION );
	}

	/**
	 * The workshop post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'workshop' ), 'workshop post type should exist.' );
	}

	/**
	 * The workshops shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'workshops' ), 'workshops shortcode should exist.' );
	}
}
