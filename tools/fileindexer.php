<?php
# Cron job: Index uploaded files (PDF, Word, ...) to table filecontent
# Assuming (!) access to commands: antiword, pdftotext, docx2txt

/* Indexed
 * 0: Ready to be indexed
 * 1: Indexed
 * 2: In queue, this script
 * 3: Skipped
 * 4: Error
 * 5: Not found
 */

chdir( __DIR__ . "/../www/");
require "rpgconnect.inc.php";
require "base.inc.php";
setlocale(LC_CTYPE, "da_DK.UTF-8"); // due to escapeshellarg()
$limit = intval($_SERVER['argv']['1'] ?? 1); // How many files should this script check in one run

define('ALEXFILEPATH','../loot.alexandria.dk/files/');
if (! is_dir(ALEXFILEPATH) ) {
	die("Directory does not exist: " . ALEXFILEPATH);
}

function getdirfromcategory($category) {
	$paths = [
		"sce" => "scenario",
		"convent" => "convent",
		"conset" => "conset",
		"tag" => "tag",
	];
	return $paths[$category];
}

function checkArchiveFile($path) {
	if (substr($path, -1) == '/') return false; // directory
	if (substr($path, 0, 9) == '__MACOSX/') return false; // Mac resource forks
	if (substr($path, -9) == '.DS_Store') return false; // Mac custom attributes
	return true;
}

$files = getall("SELECT id, data_id, category, filename FROM files WHERE indexed = 0 AND downloadable = 1 LIMIT $limit");
if ( ! $files) {
	exit;
}
$ids = [];
foreach ($files AS $file) {
	$ids[] = $file['id'];
}
doquery("UPDATE files SET indexed = 2 WHERE id IN(" . implode( ",", $ids ) . ")");

function indexFile($file, $archivefile = NULL, $tmpfile = NULL) {
	$filepath = ALEXFILEPATH . getdirfromcategory($file['category']) . '/' . $file['data_id'] . '/' . $file['filename'];
	if ($tmpfile) {
		$filepathoriginal = $filepath;
		$filepath = $tmpfile;
	}
	$extension = strtolower( substr( strrchr( ($archivefile ? $archivefile : $filepath), "." ), 1 ) );
	print "Checking " . ($archivefile ? $filepathoriginal . ' -> ' . $archivefile : $filepath ) . PHP_EOL;
	if ( ! file_exists($filepath) ) {
		print "File did not exist, skipping." . PHP_EOL;
		return 5;
	}
	if ($extension == "pdf") {
		$command = "pdftotext ".escapeshellarg($filepath)." -";
		$content = `$command`;
	} elseif ($extension == "doc") {
		$command = "antiword ".escapeshellarg($filepath);
		$content = `$command`;
	} elseif ($extension == "docx") {
		$command = "docx2txt ".escapeshellarg($filepath) . " -";
		$content = `$command`;
	} elseif ($extension == "txt") {
		$content = file_get_contents($filepath);
	} elseif ($extension == "zip" && $archivefile == NULL) { // Only descent one level into zip files
		$zip = new ZipArchive;
		$list = $zip->open($filepath);
		if ($list !== TRUE) {
			print "Can't read zip file (error: " . $list . "). Skipping." . PHP_EOL;
			return 3;
		}
		for($i = 0; $i < $zip->numFiles; $i++) {
			$archivefile = $zip->getNameIndex($i);
			if (!checkArchiveFile($archivefile)) {
				continue;
			}
			$tmpfile = tempnam( sys_get_temp_dir(), 'alexandria_fileindex_');
			copy("zip://".$filepath."#".$archivefile, $tmpfile);
			indexFile($file, $archivefile, $tmpfile);
			unlink($tmpfile);
		}
		return 1;

	} else {
		print "File is not PDF, DOC, TXT, ZIP. Skipping." . PHP_EOL;
		return 3;
	}
	// make sure content is UTF-8
	$encoding = mb_detect_encoding($content, 'UTF-8,ISO-8859-1');
	if ($encoding != 'UTF-8') {
		$content = utf8_encode($content);
	}
	$pages = explode("\x0c",$content); // Split by Form feed control character
	$numpages = 0;
	foreach($pages AS $page => $text) {
		if ($text) {
			$numpages++;
			$archivefilevalue = ($archivefile ? "'" . dbesc($archivefile) . "'" : 'NULL' );
			$label = ($page + 1);
			$sql = "INSERT INTO filedata (files_id, label, content, archivefile) VALUES (" . $file['id'] . ", '" . $label . "', '".dbesc($text)."', $archivefilevalue)";
			doquery($sql);
			$error = dberror();
			if ( $error ) {
				print "Page error: " . $page . ", " . $error . PHP_EOL;
			}
		}
	}
	print "File indexed! ($numpages pages) " . dberror() . PHP_EOL;
	return 1;
}

// File by file
foreach ($files AS $file) {
	$indexed = indexFile($file);
	doquery("UPDATE files SET indexed = $indexed WHERE id = " .$file['id']);
}
?>
