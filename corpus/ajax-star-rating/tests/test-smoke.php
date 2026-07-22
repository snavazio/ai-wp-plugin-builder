<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Arsr
 */

/**
 * AJAX Star Rating smoke test.
 */
class Arsr_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ARSR_VERSION' ), 'ARSR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ARSR_VERSION );
	}

	/**
	 * The star_rating shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'star_rating' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_star_rating_submit' ) );
	}
}
