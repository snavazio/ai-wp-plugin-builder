<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Blib
 */

/**
 * Book Library smoke test.
 */
class Blib_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BLIB_VERSION' ), 'BLIB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BLIB_VERSION );
	}

	/**
	 * The book post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'blib_book' ), 'blib_book post type should exist.' );
	}

	/**
	 * The author taxonomy exists.
	 *
	 * @return void
	 */
	public function test_author_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'blib_author' ), 'blib_author taxonomy should exist.' );
	}

	/**
	 * The genre taxonomy exists.
	 *
	 * @return void
	 */
	public function test_genre_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'blib_genre' ), 'blib_genre taxonomy should exist.' );
	}

	/**
	 * The library shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'library' ), 'library shortcode should exist.' );
	}
}
