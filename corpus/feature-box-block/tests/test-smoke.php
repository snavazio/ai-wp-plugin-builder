<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Fbox
 */

/**
 * Feature Box Block smoke test.
 */
class Fbox_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'FBOX_VERSION' ), 'FBOX_VERSION should be defined.' );
		$this->assertSame( '1.0.0', FBOX_VERSION );
	}

	/**
	 * The Feature Box Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'fbox/feature-box' ) );
	}
}
