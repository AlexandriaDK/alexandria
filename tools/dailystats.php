<?php
// Output CSV with daily stats
chdir( __DIR__ . "/../www/");
require "rpgconnect.inc.php";
require "base.inc.php";

$date = date("Y-m-d");
$scenarios = getone("SELECT COUNT(*) FROM game WHERE boardgame = 0");
$persons = getone("SELECT COUNT(*) FROM person");
$conventions = getone("SELECT COUNT(*) FROM convention");
$rpgsystems = getone("SELECT COUNT(*) FROM gamesystem");
$downloads = getone("SELECT COUNT(DISTINCT game_id) FROM files WHERE downloadable = 1");
$boardgames = getone("SELECT COUNT(*) FROM game WHERE boardgame = 1");
$conseries = getone("SELECT COUNT(*) FROM conset");
$users = getone("SELECT COUNT(*) FROM users");
$editors = getone("SELECT COUNT(*) FROM users WHERE editor = 1");
$magazines = getone("SELECT COUNT(*) FROM magazine");
$issues = getone("SELECT COUNT(*) FROM issue");
$articles = getone("SELECT COUNT(*) FROM article");
$references = getone("SELECT COUNT(*) FROM article_reference");
$locations = getone("SELECT COUNT(*) FROM locations");

print implode(",", [ $date, $scenarios, $persons, $conventions, $rpgsystems, $downloads, $boardgames, $conseries, $users, $editors, $magazines, $issues, $articles, $references, $locations ] ) . PHP_EOL;

?>
