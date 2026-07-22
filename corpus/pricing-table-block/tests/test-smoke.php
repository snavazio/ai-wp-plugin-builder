<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Ptbl
 */

/**
 * Pricing Table Block smoke test.
 */
class Ptbl_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PTBL_VERSION' ), 'PTBL_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PTBL_VERSION );
	}

	/**
	 * The Pricing Table Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'pricing-table-block/pricing-table' ), 'Pricing Table block should be registered.' );
	}
}
