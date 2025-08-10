<?php
// Download missing files from download.alexandria.dk
chdir(__DIR__ . "/../www/");
require "rpgconnect.inc.php";
require "base.inc.php";
define('ALEXFILEPATH', '../loot.alexandria.dk/files/');
define('ALEXURL', 'https://download.alexandria.dk/files/');

$files = getall("SELECT id, COALESCE(game_id, convention_id, conset_id, gamesystem_id, tag_id, issue_id) AS data_id, CASE WHEN !ISNULL(game_id) THEN 'game' WHEN !ISNULL(convention_id) THEN 'convention' WHEN !ISNULL(conset_id) THEN 'conset' WHEN !ISNULL(gamesystem_id) THEN 'gamesystem' WHEN !ISNULL(tag_id) THEN 'tag' WHEN !ISNULL(issue_id) THEN 'issue' END AS category, filename FROM files WHERE downloadable = 1");

foreach ($files as $file) {
  $categorydir = getcategorydir($file['category']);
  $folder = $categorydir . '/' . $file['data_id'] . '/';
  $path = $folder . $file['filename'];
  $folderpath = ALEXFILEPATH . $folder;
  $filepath = ALEXFILEPATH . $path;
  $urlpath = ALEXURL . $folder . rawurlencode($file['filename']);
  if (! file_exists($filepath)) {
    if (! file_exists($folderpath)) {
      print "Creating directory: " . $folderpath . PHP_EOL;
      mkdir($folderpath);
    }
    print "Downloading from: " . $urlpath . PHP_EOL;
    $filedata = file_get_contents($urlpath);
    print "Saving to: " . $filepath . PHP_EOL;
    if (strlen($filedata) == 0) {
      print "Error: File is empty." . PHP_EOL;
    } else {
      $saved = file_put_contents($filepath, $filedata);
      if ($saved === false) {
        print "Error: Could not save file." . PHP_EOL;
      }
    }
  }
}
