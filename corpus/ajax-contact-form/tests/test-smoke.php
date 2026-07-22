<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Acff
 */

/**
 * AJAX Contact Form smoke test.
 */
class Acff_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ACFF_VERSION' ), 'ACFF_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ACFF_VERSION );
	}

	/**
	 * The contact_form shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'contact_form' ) );
	}

	/**
	 * The acff_submission post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'acff_submission' ) );
	}

	/**
	 * The AJAX handler is registered for logged-out users.
	 *
	 * @return void
	 */
	public function test_ajax_handler_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_contact_form_submit' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_contact_form_submit' ) );
	}
}
