<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Beers
 */

/**
 * Beer List smoke test.
 */
class Beers_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BEERS_VERSION' ), 'BEERS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BEERS_VERSION );
	}

	/**
	 * The beer post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'beer' ), 'beer post type should exist.' );
	}

	/**
	 * The style taxonomy exists.
	 *
	 * @return void
	 */
	public function test_taxonomy_exists() {
		$this->assertTrue( taxonomy_exists( 'style' ), 'style taxonomy should exist.' );
	}

	/**
	 * The beers shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'beers' ), 'beers shortcode should exist.' );
	}
}
