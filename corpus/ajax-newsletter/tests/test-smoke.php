<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Anews
 */

/**
 * AJAX Newsletter smoke test.
 */
class Anews_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ANEWS_VERSION' ), 'ANEWS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ANEWS_VERSION );
	}

	/**
	 * The newsletter shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'newsletter' ) );
	}

	/**
	 * The AJAX handler is registered for logged-in users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_anews_submit' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_anews_submit' ) );
	}
}
