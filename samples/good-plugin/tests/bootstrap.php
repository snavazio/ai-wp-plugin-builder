<?php
/**
 * PHPUnit bootstrap for the Client Notes smoke test.
 *
 * Runs inside the wp-env `tests-cli` container, which provides the WordPress
 * PHPUnit test library. We locate it via WP_TESTS_DIR / WP_PHPUNIT__DIR and
 * fall back to the conventional /wordpress-phpunit mount.
 *
 * @package Good_Plugin
 */

$cnote_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $cnote_tests_dir ) {
	$cnote_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}
if ( ! $cnote_tests_dir || ! file_exists( $cnote_tests_dir . '/includes/functions.php' ) ) {
	foreach ( array( '/wordpress-phpunit', '/var/www/html/wp-content/plugins/wordpress-develop/tests/phpunit', '/tmp/wordpress-tests-lib' ) as $cnote_candidate ) {
		if ( file_exists( $cnote_candidate . '/includes/functions.php' ) ) {
			$cnote_tests_dir = $cnote_candidate;
			break;
		}
	}
}

if ( ! $cnote_tests_dir || ! file_exists( $cnote_tests_dir . '/includes/functions.php' ) ) {
	fwrite( STDERR, "Could not locate the WordPress test library.\n" );
	exit( 1 );
}

// The WP Core test bootstrap requires the Yoast PHPUnit Polyfills. The harness dir (which has them
// installed under vendor/) is mapped into the container at wp-content/aiwpb-harness.
if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	$cnote_polyfills = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
	if ( ! $cnote_polyfills ) {
		$cnote_polyfills = '/var/www/html/wp-content/aiwpb-harness/vendor/yoast/phpunit-polyfills';
	}
	if ( is_dir( $cnote_polyfills ) ) {
		define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $cnote_polyfills );
	}
}

require_once $cnote_tests_dir . '/includes/functions.php';

/**
 * Load this plugin before WordPress finishes booting the test environment.
 *
 * @return void
 */
function cnote_manually_load_plugin() {
	require dirname( __DIR__ ) . '/good-plugin.php';
}
tests_add_filter( 'muplugins_loaded', 'cnote_manually_load_plugin' );

require $cnote_tests_dir . '/includes/bootstrap.php';
