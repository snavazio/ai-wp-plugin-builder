<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Accb
 */

/**
 * Accordion Block smoke test.
 */
class Accb_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ACCB_VERSION' ), 'ACCB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ACCB_VERSION );
	}

	/**
	 * The Accordion Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'accordion-block/accordion-item' ), 'Accordion Block should be registered.' );
	}
}
