<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Rsblock
 */

/**
 * Rating Stars Block smoke test.
 */
class Rsblock_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'RSBLOCK_VERSION' ), 'RSBLOCK_VERSION should be defined.' );
		$this->assertSame( '1.0.0', RSBLOCK_VERSION );
	}

	/**
	 * The Rating Stars Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'rating-stars-block/rating-stars' ), 'Rating Stars block should be registered.' );
	}
}
