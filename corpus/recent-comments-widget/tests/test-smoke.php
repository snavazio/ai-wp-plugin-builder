<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rcws
 */

/**
 * Recent Comments Widget smoke test.
 */
class Rcws_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RCWS_VERSION' ), 'RCWS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RCWS_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'RCWS_Recent_Comments_Widget' ), 'RCWS_Recent_Comments_Widget class should exist.' );
	}
}
