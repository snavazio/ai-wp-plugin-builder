<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Evcal
 */

/**
 * Events Calendar smoke test.
 */
class Evcal_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'EVCAL_VERSION' ), 'EVCAL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', EVCAL_VERSION );
	}

	/**
	 * The event post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'event' ), 'event post type should exist.' );
	}

	/**
	 * The events shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'events' ), 'events shortcode should exist.' );
	}
}
