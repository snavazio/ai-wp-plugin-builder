<?php
/**
 * Extract a .zip to a directory (used by the ingest flow). Usage: php unzip.php <zip> <destDir>
 *
 * @package AIWPB
 */

if ( $argc < 3 ) {
	fwrite( STDERR, "Usage: php unzip.php <zip> <destDir>\n" );
	exit( 2 );
}
$zip = new ZipArchive();
if ( true !== $zip->open( $argv[1] ) ) {
	fwrite( STDERR, "Could not open zip: {$argv[1]}\n" );
	exit( 1 );
}
if ( ! is_dir( $argv[2] ) ) {
	mkdir( $argv[2], 0775, true );
}
$zip->extractTo( $argv[2] );
$zip->close();
echo "extracted {$argv[1]} -> {$argv[2]}\n";
exit( 0 );
