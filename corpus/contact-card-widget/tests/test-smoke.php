<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ccw1
 */

/**
 * Contact Card Widget smoke test.
 */
class Ccw1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CCW1_VERSION' ), 'CCW1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CCW1_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'CCW1_Contact_Card_Widget' ), 'CCW1_Contact_Card_Widget class should exist.' );
	}
}
