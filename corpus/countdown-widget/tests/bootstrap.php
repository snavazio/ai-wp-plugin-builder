<?php
/**
 * PHPUnit bootstrap. Runs inside the wp-env tests-cli container, which provides the WordPress
 * PHPUnit test library. Locates it via WP_TESTS_DIR / WP_PHPUNIT__DIR with a /wordpress-phpunit fallback,
 * and points the WP core bootstrap at the Yoast PHPUnit Polyfills mapped through the harness dir.
 *
 * @package Cdwgt
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}
if ( ! $_tests_dir || ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	foreach ( array( '/wordpress-phpunit', '/tmp/wordpress-tests-lib' ) as $_candidate ) {
		if ( file_exists( $_candidate . '/includes/functions.php' ) ) {
			$_tests_dir = $_candidate;
			break;
		}
	}
}

if ( ! $_tests_dir || ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	fwrite( STDERR, "Could not locate the WordPress test library.\n" );
	exit( 1 );
}

if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	$_polyfills = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
	if ( ! $_polyfills ) {
		$_polyfills = '/var/www/html/wp-content/aiwpb-harness/vendor/yoast/phpunit-polyfills';
	}
	if ( is_dir( $_polyfills ) ) {
		define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_polyfills );
	}
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Load this plugin before WordPress finishes booting the test environment.
 *
 * @return void
 */
function cdwgt_manually_load_plugin() {
	require dirname( __DIR__ ) . '/countdown-widget.php';
}
tests_add_filter( 'muplugins_loaded', 'cdwgt_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
