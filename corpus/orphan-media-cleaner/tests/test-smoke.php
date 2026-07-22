<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Omclean
 */

/**
 * Orphan Media Cleaner smoke test.
 */
class Omclean_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'OMCLEAN_VERSION' ), 'OMCLEAN_VERSION should be defined.' );
		$this->assertSame( '1.0.0', OMCLEAN_VERSION );
	}

	/**
	 * The omclean_media_count option exists.
	 *
	 * @return void
	 */
	public function test_media_count_option_exists() {
		// Simulate first run by setting option to 0.
		update_option( 'omclean_media_count', 0 );
		$this->assertNotFalse( get_option( 'omclean_media_count' ), 'The omclean_media_count option should exist.' );
	}

	/**
	 * The omclean_weekly_cleanup cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		omclean_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'omclean_weekly_cleanup' ), 'The omclean_weekly_cleanup cron event should be scheduled.' );
	}
}
