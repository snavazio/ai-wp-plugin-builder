<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Daily
 */

/**
 * Daily Quote Rotator smoke test.
 */
class Daily_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'DAILY_VERSION' ), 'DAILY_VERSION should be defined.' );
		$this->assertSame( '1.0.0', DAILY_VERSION );
	}

	/**
	 * The quote_of_day shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'quote_of_day' ) );
	}
}
