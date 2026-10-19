<?php
/**
 * Zip a plugin's FULL source (tests, SPEC.json, phpunit config included) under a single root folder named
 * <slug>. Unlike package.php this ignores .distignore: it is the source snapshot kept with each version and
 * pushed to GitHub, not the installable package. Dependency and cache folders are still skipped.
 *
 * Usage: php zipdir.php <dir> <slug> <outZipPath>
 *
 * @package AIWPB
 */

if ( $argc < 4 ) {
	fwrite( STDERR, "Usage: php zipdir.php <dir> <slug> <outZipPath>\n" );
	exit( 2 );
}

$dir  = rtrim( $argv[1], '/' );
$slug = $argv[2];
$out  = $argv[3];

if ( ! is_dir( $dir ) ) {
	fwrite( STDERR, "Not a directory: $dir\n" );
	exit( 1 );
}

$skip_dirs  = array( '.git', 'node_modules', 'vendor' );
$skip_files = array( '.phpunit.result.cache' );

if ( file_exists( $out ) ) {
	unlink( $out );
}
$zip = new ZipArchive();
if ( true !== $zip->open( $out, ZipArchive::CREATE ) ) {
	fwrite( STDERR, "Could not create zip: $out\n" );
	exit( 1 );
}

$it = new RecursiveIteratorIterator(
	new RecursiveCallbackFilterIterator(
		new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
		function ( $file ) use ( $skip_dirs, $skip_files ) {
			$name = $file->getFilename();
			return $file->isDir() ? ! in_array( $name, $skip_dirs, true ) : ! in_array( $name, $skip_files, true );
		}
	),
	RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
foreach ( $it as $file ) {
	$rel = str_replace( DIRECTORY_SEPARATOR, '/', substr( $file->getPathname(), strlen( $dir ) + 1 ) );
	if ( $file->isDir() ) {
		$zip->addEmptyDir( $slug . '/' . $rel );
	} else {
		$zip->addFile( $file->getPathname(), $slug . '/' . $rel );
		$count++;
	}
}
$zip->close();
fwrite( STDOUT, "Zipped $count source files into $out (root: $slug/)\n" );
exit( 0 );
