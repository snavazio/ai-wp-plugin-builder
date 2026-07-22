<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cdwgt
 */

/**
 * Countdown Widget smoke test.
 */
class Cdwgt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CDWGT_VERSION' ), 'CDWGT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CDWGT_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'CDWGT_Widget' ), 'CDWGT_Widget class should exist.' );
	}
}
