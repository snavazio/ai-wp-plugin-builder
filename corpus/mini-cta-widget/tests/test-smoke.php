<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Mctw
 */

/**
 * Mini CTA Widget smoke test.
 */
class Mctw_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'MCTW_VERSION' ), 'MCTW_VERSION should be defined.' );
		$this->assertSame( '1.0.0', MCTW_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'MCTW_CTA_Widget' ), 'MCTW_CTA_Widget class should exist.' );
	}
}
