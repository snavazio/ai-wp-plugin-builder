<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ppwgt
 */

/**
 * Popular Posts Widget smoke test.
 */
class Ppwgt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PPWGT_VERSION' ), 'PPWGT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PPWGT_VERSION );
	}

	/**
	 * The plugin registered the widget.
	 *
	 * @return void
	 */
	public function test_widget_registered() {
		$this->assertTrue( class_exists( 'PPWGT_Popular_Posts_Widget' ), 'PPWGT_Popular_Posts_Widget class should exist.' );
	}
}
