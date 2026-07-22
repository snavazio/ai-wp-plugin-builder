<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Comics
 */

/**
 * Comics smoke test.
 */
class Comics_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'COMICS_VERSION' ), 'COMICS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', COMICS_VERSION );
	}

	/**
	 * The comic post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'comic' ), 'comic post type should exist.' );
	}

	/**
	 * The comics_publisher taxonomy exists.
	 *
	 * @return void
	 */
	public function test_publisher_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'comics_publisher' ), 'comics_publisher taxonomy should exist.' );
	}

	/**
	 * The comics_series taxonomy exists.
	 *
	 * @return void
	 */
	public function test_series_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'comics_series' ), 'comics_series taxonomy should exist.' );
	}

	/**
	 * The comics_series shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'comics_series' ), 'comics_series shortcode should exist.' );
	}
}
