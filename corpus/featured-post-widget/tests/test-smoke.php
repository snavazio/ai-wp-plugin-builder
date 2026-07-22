<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Fpw1
 */

/**
 * Featured Post Widget smoke test.
 */
class Fpw1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FPW1_VERSION' ), 'FPW1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FPW1_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'Fpw1_Featured_Post_Widget' ), 'Fpw1_Featured_Post_Widget class should exist.' );
	}
}
