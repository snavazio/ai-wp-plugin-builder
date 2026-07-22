<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Qsrch
 */

/**
 * Quick Search smoke test.
 */
class Qsrch_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'QSRCH_VERSION' ), 'QSRCH_VERSION should be defined.' );
		$this->assertSame( '1.0.0', QSRCH_VERSION );
	}

	/**
	 * The quick_search shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'quick_search' ) );
	}

	/**
	 * The AJAX handler is registered for logged-in users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertTrue( has_action( 'wp_ajax_quick_search_suggestions' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertTrue( has_action( 'wp_ajax_nopriv_quick_search_suggestions' ) );
	}
}
