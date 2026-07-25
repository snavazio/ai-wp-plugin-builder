<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Gyti
 */

/**
 * Google YouTube Transcript Importer smoke test.
 */
class Gyti_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'GYTI_VERSION' ), 'GYTI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', GYTI_VERSION );
	}
}
