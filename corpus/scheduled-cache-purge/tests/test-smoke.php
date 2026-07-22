<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Scpc
 */

/**
 * Scheduled Cache Purge smoke test.
 */
class Scpc_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SCPC_VERSION' ), 'SCPC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SCPC_VERSION );
	}

	/**
	 * The scp_daily_purge cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		scpc_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'scp_daily_purge' ), 'The scp_daily_purge cron event should be scheduled.' );
	}
}
