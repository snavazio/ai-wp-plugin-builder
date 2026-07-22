<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ctaw
 */

/**
 * Call To Action Widget smoke test.
 */
class Ctaw_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CTAW_VERSION' ), 'CTAW_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CTAW_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'CTAW_CTA_Widget' ), 'CTAW_CTA_Widget class should exist.' );
	}
}
