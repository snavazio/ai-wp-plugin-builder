<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Mrev
 */

/**
 * Movie Reviews smoke test.
 */
class Mrev_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'MREV_VERSION' ), 'MREV_VERSION should be defined.' );
		$this->assertSame( '1.0.0', MREV_VERSION );
	}

	/**
	 * The movie post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'movie' ), 'movie post type should exist.' );
	}

	/**
	 * The mrev_genre taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'mrev_genre' ), 'mrev_genre taxonomy should exist.' );
	}

	/**
	 * The movies shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'movies' ), 'movies shortcode should exist.' );
	}
}
