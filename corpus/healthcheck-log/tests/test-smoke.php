<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Hlck
 */

/**
 * Healthcheck Log smoke test.
 */
class Hlck_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'HLCK_VERSION' ), 'HLCK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', HLCK_VERSION );
	}

	/**
	 * The healthcheck shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'healthcheck' ) );
	}

	/**
	 * The healthcheck_daily cron event is scheduled.
	 *
	 * @return void
	 */
	public function test_cron_event_scheduled() {
		$this->assertNotFalse( wp_get_schedule( 'healthcheck_daily' ) );
	}
}
