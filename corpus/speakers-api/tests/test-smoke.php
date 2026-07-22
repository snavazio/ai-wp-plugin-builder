<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Spkr
 */

/**
 * Speakers API smoke test.
 */
class Spkr_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SPKR_VERSION' ), 'SPKR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SPKR_VERSION );
	}

	/**
	 * The speaker post type exists.
	 *
	 * @return void
	 */
	public function test_speaker_post_type_exists() {
		$this->assertTrue( post_type_exists( 'spkr_speaker' ), 'Speaker post type should exist.' );
	}
}
