<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Stats
 */

/**
 * Stats Snapshot smoke test.
 */
class Stats_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'STATS_VERSION' ), 'STATS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', STATS_VERSION );
	}

	/**
	 * The stats_snapshot shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'stats_snapshot' ) );
	}
}
