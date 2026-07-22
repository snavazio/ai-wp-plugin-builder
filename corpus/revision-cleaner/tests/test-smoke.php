<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Revcln
 */

/**
 * Revision Cleaner smoke test.
 */
class Revcln_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'REVCLN_VERSION' ), 'REVCLN_VERSION should be defined.' );
		$this->assertSame( '1.0.0', REVCLN_VERSION );
	}

	/**
	 * The revcln_revision_cleaner_weekly_cron cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		revcln_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'revcln_revision_cleaner_weekly_cron' ), 'The revcln_revision_cleaner_weekly_cron cron event should be scheduled.' );
	}

	/**
	 * The revcln_deleted_count option exists.
	 *
	 * @return void
	 */
	public function test_deleted_count_option() {
		// Simulate the first run by setting the option to 0.
		update_option( 'revcln_deleted_count', 0 );
		$this->assertNotFalse( get_option( 'revcln_deleted_count' ), 'The revcln_deleted_count option should exist.' );
	}
}
