<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Artw
 */

/**
 * Artworks smoke test.
 */
class Artw_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ARTW_VERSION' ), 'ARTW_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ARTW_VERSION );
	}

	/**
	 * The artwork post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'artw_artwork' ), 'artw_artwork post type should exist.' );
	}

	/**
	 * The artworks shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'artw_artworks' ), 'artw_artworks shortcode should exist.' );
	}
}
