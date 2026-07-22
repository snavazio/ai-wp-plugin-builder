<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rpwgt
 */

/**
 * Recent Products Widget smoke test.
 */
class Rpwgt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RPWGT_VERSION' ), 'RPWGT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RPWGT_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'Rpwgt_Recent_Products_Widget' ), 'Rpwgt_Recent_Products_Widget class should exist.' );
	}
}
