<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Gigc
 */

/**
 * Gigs smoke test.
 */
class Gigc_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'GIGC_VERSION' ), 'GIGC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', GIGC_VERSION );
	}

	/**
	 * The gigc_gig post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'gigc_gig' ), 'gigc_gig post type should exist.' );
	}

	/**
	 * The gigs shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'gigs' ), 'gigs shortcode should exist.' );
	}
}
