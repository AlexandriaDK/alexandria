<?php
# Cron job: Index uploaded files (PDF, Word, ...) to table filecontent

# Assuming (!) access to commands  antiword  and  pdftotext

/* Indexed
 * 0: Ready to be indexed
 * 1: Indexed
 * 2: In queue, this script
 * 3: Skipped
 * 4: Error
 * 5: Not found
 */

chdir("../www/");
require "rpgconnect.inc.php";
require "base.inc.php";
setlocale(LC_CTYPE, "da_DK.UTF-8"); // due to escapeshellarg()
$limit = intval($_SERVER['argv']['1'] ?? 1); // How many files should this script check in one run

define('ALEXFILEPATH','/home/penguin/Dokumenter/dev/alexandria/loot.alexandria.dk/files/');

$paths = [
	"sce" => "scenario",
	"convent" => "convent",
	"conset" => "conset"
];

$files = getall("SELECT id, data_id, category, filename FROM files WHERE indexed = 0 AND downloadable = 1 LIMIT $limit");
if ( ! $files) {
	exit;
}
$ids = [];
foreach ($files AS $file) {
	$ids[] = $file['id'];
}
doquery("UPDATE files SET indexed = 2 WHERE id IN(" . implode( ",", $ids ) . ")");

// File by file
foreach ($files AS $file) {
	$filepath = ALEXFILEPATH . $paths[$file['category']] . '/' . $file['data_id'] . '/' . $file['filename'];
	$extension = strtolower( substr( strrchr( $filepath, "." ), 1 ) );
	print "Checking " . $filepath . PHP_EOL;
	if ( ! file_exists($filepath) ) {
		print "File did not exist, skipping." . PHP_EOL;
		doquery("UPDATE files SET indexed = 5 WHERE id = " .$file['id']);
		continue;
	}
	if ($extension == "pdf") {
		$command = "pdftotext ".escapeshellarg($filepath)." -";
		$content = `$command`;
	} elseif ($extension == "doc") {
		$command = "antiword ".escapeshellarg($filepath);
		$content = `$command`;
	} elseif ($extension == "txt") {
		$content = file_get_contents($filepath);
	} else {
		print "File is not PDF, DOC, TXT. Skipping." . PHP_EOL;
		doquery("UPDATE files SET indexed = 3 WHERE id = " .$file['id']);
		continue;
	}
	$pages = explode("\x0c",$content); // Split by Form feed control character

	$numpages = 0;
	foreach($pages AS $page => $text) {
		if ($text) {
			$numpages++;
			$sql = "INSERT INTO filedata (files_id, label, content) VALUES (" . $file['id'] . ", '" . ( $page + 1 ) . "', '".dbesc($text)."')";
			doquery($sql);
			$error = dberror();
			if ( $error ) {
				print "Page error: " . $page . ", " . $error . PHP_EOL;

			}
		}
	}
	doquery("UPDATE files SET indexed = 1 WHERE id = " .$file['id']);
	print "File indexed! ($numpages pages) " . dberror() . PHP_EOL;
	
}
?>
