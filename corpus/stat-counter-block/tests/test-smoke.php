<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Scblock
 */

/**
 * Stat Counter Block smoke test.
 */
class Scblock_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'SCBLOCK_VERSION' ), 'SCBLOCK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', SCBLOCK_VERSION );
	}

	/**
	 * The Stat Counter Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'scblock/stat-counter' ), 'Stat Counter block should be registered.' );
	}
}
