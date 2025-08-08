<?php
die("No updated to new db scheme");

require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$paths = array(
  "game" => "scenario",
  "convention" => "convent",
  "conset" => "conset"
);

htmladmstart("Missing files");

print "<h1>Cleanup for missing files due to server crash.</h1>";


$dbfiles = getall("SELECT id, data_id, category, filename, description, downloadable, inserted, language FROM files WHERE downloadable = 1 ORDER BY category, data_id");

$count = 0;
$html = "<p>";
foreach ($dbfiles as $dbfile) {
  $output = '';
  $upload_path = DOWNLOAD_PATH . $paths[$dbfile['category']] . "/" . $dbfile['data_id'] . "/" . $dbfile['filename'];
  if (!file_exists($upload_path)) {
    $count++;
    $output = '<a href="files.php?category=' . $dbfile['category'] . '&data_id=' . $dbfile['data_id'] . '"><b>' . substr($upload_path, strpos($upload_path, 'loot')) . '</b></a><br>' . PHP_EOL;
  }
  $html .= $output;
}
$html .= "</p>";

print "<p>Missing files: " . $count . "</p>";
print $html;

htmladmend();
