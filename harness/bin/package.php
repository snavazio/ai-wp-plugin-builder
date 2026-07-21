<?php
/**
 * Self-contained plugin packager. Zips a plugin directory into a distributable .zip with a single
 * root folder named exactly the slug, honoring the plugin's .distignore (so dev files are excluded).
 *
 * Usage: php package.php <pluginDir> <slug> <outZipPath>
 */

if ( $argc < 4 ) {
	fwrite( STDERR, "Usage: php package.php <pluginDir> <slug> <outZipPath>\n" );
	exit( 2 );
}

$plugin_dir = rtrim( $argv[1], '/' );
$slug       = $argv[2];
$out_zip     = $argv[3];

if ( ! is_dir( $plugin_dir ) ) {
	fwrite( STDERR, "Plugin dir not found: $plugin_dir\n" );
	exit( 1 );
}

// Parse .distignore into a list of anchored patterns (leading '/' relative to plugin root).
$patterns = array();
$distignore = $plugin_dir . '/.distignore';
if ( is_file( $distignore ) ) {
	foreach ( file( $distignore, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
		$line = trim( $line );
		if ( '' === $line || 0 === strpos( $line, '#' ) ) {
			continue;
		}
		$patterns[] = ltrim( $line, '/' );
	}
}

/**
 * Decide whether a plugin-relative path is excluded by the .distignore patterns.
 */
function aiwpb_is_excluded( $rel, $patterns ) {
	foreach ( $patterns as $p ) {
		if ( $rel === $p || 0 === strpos( $rel, $p . '/' ) ) {
			return true;
		}
		if ( fnmatch( $p, $rel ) || fnmatch( $p, basename( $rel ) ) ) {
			return true;
		}
	}
	return false;
}

@unlink( $out_zip );
$zip = new ZipArchive();
if ( true !== $zip->open( $out_zip, ZipArchive::CREATE ) ) {
	fwrite( STDERR, "Could not create zip: $out_zip\n" );
	exit( 1 );
}

$it = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $plugin_dir, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
foreach ( $it as $file ) {
	$rel = substr( $file->getPathname(), strlen( $plugin_dir ) + 1 );
	$rel = str_replace( DIRECTORY_SEPARATOR, '/', $rel );
	if ( aiwpb_is_excluded( $rel, $patterns ) ) {
		continue;
	}
	if ( $file->isDir() ) {
		$zip->addEmptyDir( $slug . '/' . $rel );
	} else {
		$zip->addFile( $file->getPathname(), $slug . '/' . $rel );
		$count++;
	}
}

$zip->close();
fwrite( STDOUT, "Packaged $count files into $out_zip (root: $slug/)\n" );
exit( 0 );
