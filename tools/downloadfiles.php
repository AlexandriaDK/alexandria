<?php
// Download missing files from download.alexandria.dk
chdir( __DIR__ . "/../www/");
require "rpgconnect.inc.php";
require "base.inc.php";
define('ALEXFILEPATH','../loot.alexandria.dk/files/');
define('ALEXURL','https://download.alexandria.dk/files/');

$files = getall("SELECT id, data_id, category, filename FROM files WHERE downloadable = 1");

foreach ( $files AS $file ) {
	$categorydir = getcategorydir( $file['category'] );
	$folder = $categorydir . '/' . $file['data_id'] . '/';
	$path = $folder . $file['filename'];
	$folderpath = ALEXFILEPATH . $folder;
	$filepath = ALEXFILEPATH . $path;
	$urlpath = ALEXURL . $folder . rawurlencode( $file['filename'] );
	if ( ! file_exists( $filepath ) ) {
		if ( ! file_exists( $folderpath ) ) {
			print "Creating directory: " . $folderpath . PHP_EOL;
			mkdir( $folderpath );
		}
		print "Downloading from: " . $urlpath . PHP_EOL;
		$filedata = file_get_contents( $urlpath );
		print "Saving to: " . $filepath . PHP_EOL;
		$saved = file_put_contents( $filepath, $filedata );
		if ( $saved === FALSE ) {
			print "Error: Could not save file." . PHP_EOL;
		}
	}
}

?>
