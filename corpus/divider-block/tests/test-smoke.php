<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Divb
 */

/**
 * Divider Block smoke test.
 */
class Divb_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'DIVB_VERSION' ), 'DIVB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', DIVB_VERSION );
	}

	/**
	 * The Divider Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'divb/divider-block' ), 'Divider Block should be registered.' );
	}
}
