<?php
// Output CSV with daily stats
chdir( __DIR__ . "/../www/");
require "rpgconnect.inc.php";
require "base.inc.php";

$date = date("Y-m-d");
$scenarios = getone("SELECT COUNT(*) FROM sce WHERE boardgame = 0");
$persons = getone("SELECT COUNT(*) FROM aut");
$conventions = getone("SELECT COUNT(*) FROM convent");
$rpgsystems = getone("SELECT COUNT(*) FROM sys");
$downloads = getone("SELECT COUNT(DISTINCT data_id) FROM files WHERE category = 'sce' AND downloadable = 1");
$boardgames = getone("SELECT COUNT(*) FROM sce WHERE boardgame = 1");
$conseries = getone("SELECT COUNT(*) FROM conset");
$users = getone("SELECT COUNT(*) FROM users");
$editors = getone("SELECT COUNT(*) FROM users WHERE editor = 1");

print implode(",", [ $date, $scenarios, $persons, $conventions, $rpgsystems, $downloads, $boardgames, $conseries, $users, $editors ] ) . PHP_EOL;



?>
