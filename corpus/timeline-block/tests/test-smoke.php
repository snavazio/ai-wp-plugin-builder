<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Tlmb
 */

/**
 * Timeline Block smoke test.
 */
class Tlmb_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TLMB_VERSION' ), 'TLMB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', TLMB_VERSION );
	}

	/**
	 * The Timeline Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'timeline-block/timeline-entry' ), 'Timeline Block should be registered.' );
	}
}
