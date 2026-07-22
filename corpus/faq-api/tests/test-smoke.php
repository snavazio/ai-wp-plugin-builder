<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Faqapi
 */

/**
 * FAQ API smoke test.
 */
class Faqapi_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FAQAPI_VERSION' ), 'FAQAPI_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FAQAPI_VERSION );
	}

	/**
	 * The FAQ post type exists.
	 *
	 * @return void
	 */
	public function test_faq_post_type_exists() {
		$this->assertTrue( post_type_exists( 'faqapi_faq' ), 'FAQ post type should exist.' );
	}
}
