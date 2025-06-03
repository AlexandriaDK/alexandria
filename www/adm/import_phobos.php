<?php
// Insert issues for Phobos magazine
// Based on permission from Fred Førde, Spillforeningen Ares, https://spillklubb.org/
define("PHOBOS_PATH", "../../../loot.alexandria.dk/files/phobos/");
define("DOWNLOAD_PATH", "../../../loot.alexandria.dk/files/");
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
chdir("adm/");

setlocale(LC_CTYPE, "da_DK.UTF-8"); // due to ImageMagick escapeshellarg() if imagick is not installed as module
if (!function_exists('mb_basename')) {
	function mb_basename($path)
	{
		return array_reverse(explode("/", $path))[0];
	}
}


$magazine_id = 182; // Phobos magazine
$today = date("Y-m-d");

$magazines = glob(PHOBOS_PATH . "*.pdf");
natsort($magazines);

function create_file($data_id, $category, $path, $description, $downloadable, $language) {
    $allowed_extensions = ["pdf", "txt", "doc", "docx", "zip", "rar", "mp3", "pps", "jpg", "png", "gif", "webp"];
    $path = trim($path);
	$filename = mb_basename($path);
	$description = trim($description);
	$downloadable = ($downloadable ? 1 : 0);
	$extension = strtolower(substr(strrchr($path, "."), 1));
	$data_field = getFieldFromCategory($category);
	if (!file_exists($path)) {
		$_SESSION['admin']['info'] = "Error: The files does not exist: $path";
	} elseif (!in_array($extension, $allowed_extensions)) {
		$_SESSION['admin']['info'] = "Error: Not a vaild file type: $path";
	} elseif (!$data_field) {
		$_SESSION['admin']['info'] = "Error: Unknown category";
	} else {
		doquery("INSERT INTO files (`$data_field`, filename, description, downloadable, language, inserted) VALUES ('$data_id','" . dbesc($filename) . "','" . dbesc($description) . "','$downloadable','" . dbesc($language) . "', NOW() )");
		$_SESSION['admin']['info'] = "The file has been created." . dberror();
		chlog($data_id, $category, "File created: " . $filename);
	}
}


function addissue ($title, $releasedate, $releasetext, $magazine_id, $internal = '', $status = '40') { // 40 = WIP
	$q = "INSERT INTO issue " .
	     "(title, releasedate, releasetext, magazine_id, internal, status) VALUES ".
	     "('" . dbesc($title) . "', ". sqlifnull($releasedate) . ", '" . dbesc($releasetext) . "', $magazine_id, '" . dbesc($internal) . "', $status)";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($id,'issue',"Issue created: $title");
	} else {
        print "Error creating issue: " . dberror() . "\n";
    }
    return $id;
}

function addfile($data_id, $category, $path, $description, $downloadable = 1, $language = 'da') {
    // Copy file
	$basename = mb_basename($path);

	$upload_path = DOWNLOAD_PATH . getcategorydir($category) . "/" . $data_id . "/" . $basename;
	$upload_dir = dirname($upload_path);
	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0775);
	}
	if (file_exists($upload_path)) {
		print "Error: A file with this file name already exists in $upload_path\n";
        return false;
	} elseif (copy($path, $upload_path)) {
		chmod($upload_path, 0664);
		print "The file has been uploaded.\n";
		chlog($data_id, $category, "File uploaded: " . $basename);
	} else {
		print "Error: Can't save uploaded file. Make sure the web server can write to file path: $upload_path";
        return false;
	}

    // Insert file into database
    $path = trim($path);
    $allowed_extensions = ["pdf", "txt", "doc", "docx", "zip", "rar", "mp3", "pps", "jpg", "jpeg", "png", "gif", "webp"];
	if (substr($path, 0, 1) != "/") {
		$subdir = getcategorydir($category);
		$path = DOWNLOAD_PATH . $subdir . "/" . $data_id . "/" . $path;
	}
    print "Path: $path\n";
    print "Upload path: $upload_path\n";

    $filename = mb_basename($path);
	$description = trim($description);
	$downloadable = ($downloadable ? 1 : 0);
	$extension = strtolower(substr(strrchr($path, "."), 1));
	$data_field = getFieldFromCategory($category);
	if (!file_exists($upload_path)) {
		print "Error: The file does not exist: $upload_path\n";
        return false;
	} elseif (!in_array($extension, $allowed_extensions)) {
		print "Error: Not a vaild file type: $path\n";
        return false;
	} elseif (!$data_field) {
		print "Error: Unknown category\n";
        return false;
	} else {
        $ocr_indexed = 11; // add to OCR queue
		$file_id = doquery("INSERT INTO files (`$data_field`, filename, description, downloadable, language, inserted, indexed) VALUES ('$data_id','" . dbesc($filename) . "','" . dbesc($description) . "','$downloadable','" . dbesc($language) . "', NOW(), $ocr_indexed)");
		print "The file has been created." . dberror() . "\n";
		chlog($data_id, $category, "File created: " . $filename);
	}
    return $file_id;

}

function addthumbnail() {

}

function addtoOCRqueue() {

}

print "<pre>";
// print_r($magazines);

$count = 0;
$maxcount = 50;
foreach ($magazines as $magazine) {
    $filename = basename($magazine);
    $issue_number = preg_replace('/[^0-9]/', '', $filename);
    $description = "Phobos #$issue_number";
    print "Issue number: $issue_number\n";
    if (getone("SELECT 1 FROM issue WHERE title = $issue_number AND magazine_id = $magazine_id")) {
        print "Issue already exists: " . basename($magazine) . "\n";
        continue; // Skip if issue already exists
    }

    $issue_id = addissue($issue_number, null, "Phobos #$issue_number", $magazine_id, "Autoimport by Peter, $today", '40'); // 40 = WIP
    print "Issue added: $issue_number (id $issue_id)\n";
    $file_id = addfile($issue_id, 'issue', $magazine, $description, 1, 'nb');
    $count++;
    if ($count >= $maxcount) {
        break; // Limit to first 3 issues for testing
    }
    print "\n";
}
