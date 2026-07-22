<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Dctc
 */

/**
 * Draft Cleanup smoke test.
 */
class Dctc_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'DCTC_VERSION' ), 'DCTC_VERSION should be defined.' );
		$this->assertSame( '1.0.0', DCTC_VERSION );
	}

	/**
	 * The draft_cleanup_weekly cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		dctc_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'draft_cleanup_weekly' ), 'The draft_cleanup_weekly cron event should be scheduled.' );
	}
}
