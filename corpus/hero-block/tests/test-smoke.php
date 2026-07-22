<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Hblock
 */

/**
 * Hero Block smoke test.
 */
class Hblock_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'HBLOCK_VERSION' ), 'HBLOCK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', HBLOCK_VERSION );
	}

	/**
	 * The Hero Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'hero-block/hero' ), 'Hero block should be registered.' );
	}
}
