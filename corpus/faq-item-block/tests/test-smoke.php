<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Faqb
 */

/**
 * FAQ Item Block smoke test.
 */
class Faqb_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FAQB_VERSION' ), 'FAQB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FAQB_VERSION );
	}

	/**
	 * The FAQ Item Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'faq-item-block/faq-item' ), 'FAQ Item block should be registered.' );
	}
}
