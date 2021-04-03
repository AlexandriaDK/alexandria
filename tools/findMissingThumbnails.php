<?php
// Find scenarios that have "Thumbnail added" in the log but doesn't have a thumbnail file.
// This helps finding missing thumbnails from the server crash in 2020.
require __DIR__ . "/../www/rpgconnect.inc.php";
require __DIR__ . "/../www/base.inc.php";
chdir(__DIR__ . "/../www/");

$logs = getall("SELECT DISTINCT category, data_id from log WHERE note LIKE 'Thumbnail created%' ORDER BY category, data_id");

foreach($logs AS $log) {
    $thumb = hasthumbnailpic($log['data_id'], $log['category']);
    print "Checking " . $log['category'] . " - " . $log['data_id'] . ":" . (int) $thumb . PHP_EOL;
}
?>
