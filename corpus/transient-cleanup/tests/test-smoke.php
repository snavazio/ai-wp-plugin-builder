<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tcleanup
 */

/**
 * Transient Cleanup smoke test.
 */
class Tcleanup_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TCLEANUP_VERSION' ), 'TCLEANUP_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TCLEANUP_VERSION );
	}

	/**
	 * The tcleanup_daily_cron cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		tcleanup_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'tcleanup_daily_cron' ), 'The tcleanup_daily_cron cron event should be scheduled.' );
	}
}
