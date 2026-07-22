<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Spns
 */

/**
 * Sponsors API smoke test.
 */
class Spns_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SPNS_VERSION' ), 'SPNS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SPNS_VERSION );
	}

	/**
	 * The sponsor post type exists.
	 *
	 * @return void
	 */
	public function test_sponsor_post_type_exists() {
		$this->assertTrue( post_type_exists( 'spns_sponsor' ), 'Sponsor post type should exist.' );
	}
}
