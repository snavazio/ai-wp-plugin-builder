<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ilblock
 */

/**
 * Icon List Block smoke test.
 */
class Ilblock_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'ILBLOCK_VERSION' ), 'ILBLOCK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', ILBLOCK_VERSION );
	}

	/**
	 * The Icon List Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'ilblock/icon-list' ), 'Icon List Block should be registered.' );
	}
}
