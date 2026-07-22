<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Apf1
 */

/**
 * AJAX Post Filter smoke test.
 */
class Apf1_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'APF1_VERSION' ), 'APF1_VERSION should be defined.' );
		$this->assertSame( '1.0.0', APF1_VERSION );
	}

	/**
	 * The post_filter shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'post_filter' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_nopriv_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_post_filter' ) );
	}
}
