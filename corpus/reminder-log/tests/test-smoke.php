<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rlog
 */

/**
 * Reminder Log smoke test.
 */
class Rlog_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RLOG_VERSION' ), 'RLOG_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RLOG_VERSION );
	}

	/**
	 * The reminder_log shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'reminder_log' ) );
	}
}
