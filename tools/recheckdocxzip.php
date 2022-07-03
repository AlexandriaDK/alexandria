<?php
# Find zip files containing .docx files, clear their index data from filedata and stage them for re-indexing

# Alexandria didn't support .docx files at first. While it's easy to stage .docx files for indexing again existing .zip
# files could contain .docx files and still be marked as indexed. To avoid re-indexing every .zip file this script
# only finds relevant .zip files and updates the database

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

$files = getall("SELECT id, COALESCE(game_id, convention_id, conset_id, gamesystem_id, tag_id, issue_id) AS data_id, CASE WHEN !ISNULL(game_id) THEN 'game' WHEN !ISNULL(convention_id) THEN 'convention' WHEN !ISNULL(conset_id) THEN 'conset' WHEN !ISNULL(gamesystem_id) THEN 'gamesystem' WHEN !ISNULL(tag_id) THEN 'tag' WHEN !ISNULL(issue_id) THEN 'issue' END AS category, filename FROM files WHERE filename LIKE '%.zip' AND indexed = 1 AND downloadable = 1");
if ( ! $files) {
	exit;
}

foreach ($files AS $file) {
    $filepath = ALEXFILEPATH . getdirfromcategory($file['category']) . '/' . $file['data_id'] . '/' . $file['filename'];
    $fileid = $file['id'];
    // print "Opening $filepath" . PHP_EOL;
    $zip = new ZipArchive;
    $list = $zip->open($filepath);
    if ($list !== TRUE ) {
        print "Can't read zip file (error: " . $list . "). Skipping." . PHP_EOL;
        continue;
    }
    for($i = 0; $i < $zip->numFiles; $i++) {
        $archivefile = $zip->getNameIndex($i);
        if (strtolower(substr($archivefile, -5) ) === '.docx' ) {
            print "MATCH! $fileid - $filepath => $archivefile" . PHP_EOL;
            doquery("DELETE FROM filedata WHERE files_id = " . $fileid);
            doquery("UPDATE files SET indexed = 0 WHERE id = " . $fileid);
        }
    }

}
?>
