<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Gcat
 */

/**
 * Game Catalog smoke test.
 */
class Gcat_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'GCAT_VERSION' ), 'GCAT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', GCAT_VERSION );
	}

	/**
	 * The game post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'game' ), 'game post type should exist.' );
	}

	/**
	 * The gcat_platform taxonomy exists.
	 *
	 * @return void
	 */
	public function test_platform_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'gcat_platform' ), 'gcat_platform taxonomy should exist.' );
	}

	/**
	 * The gcat_genre taxonomy exists.
	 *
	 * @return void
	 */
	public function test_genre_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'gcat_genre' ), 'gcat_genre taxonomy should exist.' );
	}

	/**
	 * The games shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'games' ), 'games shortcode should exist.' );
	}
}
