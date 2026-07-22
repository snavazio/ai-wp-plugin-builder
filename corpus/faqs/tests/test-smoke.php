<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Faqs
 */

/**
 * FAQs smoke test.
 */
class Faqs_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FAQS_VERSION' ), 'FAQS_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FAQS_VERSION );
	}

	/**
	 * The faqs_faq post type exists.
	 *
	 * @return void
	 */
	public function test_post_type_exists() {
		$this->assertTrue( post_type_exists( 'faqs_faq' ), 'faqs_faq post type should exist.' );
	}

	/**
	 * The faqs shortcode exists.
	 *
	 * @return void
	 */
	public function test_shortcode_exists() {
		$this->assertTrue( shortcode_exists( 'faqs' ), 'faqs shortcode should exist.' );
	}
}
