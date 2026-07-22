<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Cdtblk
 */

/**
 * Countdown Block smoke test.
 */
class Cdtblk_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'CDTBLK_VERSION' ), 'CDTBLK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', CDTBLK_VERSION );
	}

	/**
	 * The Countdown Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'countdown-block/countdown' ), 'Countdown block should be registered.' );
	}
}
