<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Podc
 */

/**
 * Podcast Episodes smoke test.
 */
class Podc_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PODC_VERSION' ), 'PODC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PODC_VERSION );
	}

	/**
	 * The episode post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'episode' ), 'episode post type should exist.' );
	}

	/**
	 * The season taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'season' ), 'season taxonomy should exist.' );
	}

	/**
	 * The episodes shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'episodes' ), 'episodes shortcode should exist.' );
	}
}
