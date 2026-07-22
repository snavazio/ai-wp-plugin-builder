<?php
/**
 * Smoke test: assert the plugin booted and registered its features.
 *
 * @package Pccard
 */

/**
 * Profile Card Block smoke test.
 */
class Pccard_Smoke_Test extends WP_UnitTestCase {

	/**
	 * The plugin loaded and defined its version constant.
	 *
	 * @return void
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'PCCARD_VERSION' ), 'PCCARD_VERSION should be defined.' );
		$this->assertSame( '1.0.0', PCCARD_VERSION );
	}

	/**
	 * The Profile Card Block is registered.
	 *
	 * @return void
	 */
	public function test_block_registered() {
		if ( ! function_exists( 'block_type_exists' ) ) {
			$this->markTestSkipped( 'Block editor not available.' );
			return;
		}
		$this->assertNotFalse( block_type_exists( 'profile-card-block/profile-card' ), 'Profile Card block should be registered.' );
	}
}
