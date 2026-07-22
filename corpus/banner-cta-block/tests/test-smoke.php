<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Bctab
 */

/**
 * Banner CTA Block smoke test.
 */
class Bctab_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'BCTAB_VERSION' ), 'BCTAB_VERSION should be defined.' );
		$this->assertSame( '1.0.0', BCTAB_VERSION );
	}

	/**
	 * The Banner CTA Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'banner-cta-block/banner-cta' ), 'Banner CTA Block should be registered.' );
	}
}
