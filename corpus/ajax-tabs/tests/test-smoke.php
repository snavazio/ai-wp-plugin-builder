<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ajxt
 */

/**
 * AJAX Tabs smoke test.
 */
class Ajxt_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'AJXT_VERSION' ), 'AJXT_VERSION should be defined.' );
		$this->assertSame( '1.0.0', AJXT_VERSION );
	}

	/**
	 * The tabs shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'tabs' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_ajax_tabs_load' ) );
	}
}
