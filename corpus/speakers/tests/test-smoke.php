<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Spkrs
 */

/**
 * Speakers smoke test.
 */
class Spkrs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SPKRS_VERSION' ), 'SPKRS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SPKRS_VERSION );
	}

	/**
	 * The speaker post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'speaker' ), 'speaker post type should exist.' );
	}

	/**
	 * The speakers shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'speakers' ), 'speakers shortcode should exist.' );
	}
}
