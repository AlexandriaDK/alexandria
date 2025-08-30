<?php
// Download missing files from download.alexandria.dk
chdir(__DIR__ . "/../www/");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";
define('ALEXFILEPATH', '../loot.alexandria.dk/files/');
define('ALEXURL', 'https://download.alexandria.dk/files/');

$files = getall("SELECT id, COALESCE(game_id, convention_id, conset_id, gamesystem_id, tag_id, issue_id) AS data_id, CASE WHEN !ISnull(game_id) THEN 'game' WHEN !ISnull(convention_id) THEN 'convention' WHEN !ISnull(conset_id) THEN 'conset' WHEN !ISnull(gamesystem_id) THEN 'gamesystem' WHEN !ISnull(tag_id) THEN 'tag' WHEN !ISnull(issue_id) THEN 'issue' END AS category, filename FROM files WHERE downloadable = 1");

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
