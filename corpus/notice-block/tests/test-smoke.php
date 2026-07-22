<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Nblock
 */

/**
 * Notice Block smoke test.
 */
class Nblock_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'NBLOCK_VERSION' ), 'NBLOCK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', NBLOCK_VERSION );
	}

	/**
	 * The Notice Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'nblock/notice-block' ) );
	}
}
