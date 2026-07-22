<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Parch
 */

/**
 * Post Archiver smoke test.
 */
class Parch_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PARCH_VERSION' ), 'PARCH_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PARCH_VERSION );
	}

	/**
	 * The parch_weekly_archive cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		parch_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'parch_weekly_archive' ), 'The parch_weekly_archive cron event should be scheduled.' );
	}
}
