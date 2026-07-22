<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Expcl
 */

/**
 * Expired Content Cleaner smoke test.
 */
class Expcl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'EXPCL_VERSION' ), 'EXPCL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', EXPCL_VERSION );
	}

	/**
	 * The expired_content_cleaner_cron cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		// Simulate activation to schedule the cron event.
		expcl_activate();
		$this->assertNotFalse( wp_get_scheduled_event( 'expired_content_cleaner_cron' ), 'The expired_content_cleaner_cron cron event should be scheduled.' );
	}

	/**
	 * The expired_content_cleaner_count option exists.
	 *
	 * @return void
	 */
	public function test_count_option_exists() {
		// Simulate the first run by setting the option to 0.
		update_option( 'expired_content_cleaner_count', 0 );
		$this->assertNotFalse( get_option( 'expired_content_cleaner_count' ), 'The expired_content_cleaner_count option should exist.' );
	}
}
