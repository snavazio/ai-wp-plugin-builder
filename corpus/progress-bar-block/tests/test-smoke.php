<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Pbar
 */

/**
 * Progress Bar Block smoke test.
 */
class Pbar_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PBAR_VERSION' ), 'PBAR_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PBAR_VERSION );
	}

	/**
	 * The Progress Bar Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'progress-bar-block/progress-bar' ), 'Progress bar block should be registered.' );
	}
}
