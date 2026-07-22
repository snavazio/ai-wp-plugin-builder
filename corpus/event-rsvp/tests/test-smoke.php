<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ersv
 */

/**
 * Event RSVP smoke test.
 */
class Ersv_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ERSV_VERSION' ), 'ERSV_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ERSV_VERSION );
	}

	/**
	 * The event post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'event' ), 'The event post type should exist.' );
	}

	/**
	 * The event_rsvp shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'event_rsvp' ), 'The event_rsvp shortcode should exist.' );
	}
}
