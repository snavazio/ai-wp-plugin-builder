<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Aqv1
 */

/**
 * AJAX Quick View smoke test.
 */
class Aqv1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'AQV1_VERSION' ), 'AQV1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', AQV1_VERSION );
	}

	/**
	 * The quick_view shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'quick_view' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_ajax_quick_view' ) );
	}
}
