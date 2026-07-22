<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Qwid
 */

/**
 * Quote Widget smoke test.
 */
class Qwid_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'QWID_VERSION' ), 'QWID_VERSION should be defined.' );
		$this->assertSame( '1.0.0', QWID_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'QWID_Quote_Widget' ), 'QWID_Quote_Widget class should exist.' );
	}
}
