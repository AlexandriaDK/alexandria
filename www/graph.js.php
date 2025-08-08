<?php
require("./connect.php");
require_once("base.inc.php");

$name = (string) $_REQUEST['name'];
$action = (string) $_REQUEST['action'];
$author_id = (int) $_REQUEST['author_id'];
$scenario_id = (int) $_REQUEST['scenario_id'];
$title = (string) $_REQUEST['title'];

if ($name) {
  list($author_id, $name) = getrow("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM person WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($name) . "'");

  if (!$author_id) {
    die("Error");
  }
}
if ($author_id && !$name) {
  $name = getone("SELECT CONCAT(firstname, ' ', surname) AS name FROM person WHERE id = $author_id");
}

if ($title) {
  list($scenario_id, $title) = getrow("SELECT id, title FROM game WHERE title = '" . dbesc($title) . "' LIMIT 1");

  if (!$scenario_id) {
    die("Error");
  }
}

if ($scenario_id && !$title) {
  $title = getone("SELECT title FROM game WHERE id = $scenario_id");
}

if ($author_id) {
  if ($action == 'getScenarios') {
    $connections = getall("SELECT game.id, title FROM pgrel INNER JOIN game ON pgrel.game_id = game.id AND pgrel.title_id = 1 WHERE pgrel.person_id = $author_id", FALSE);
    $result = ['result' => 'scenarios', 'id' => $author_id, 'name' => $name, 'connections' => $connections];
  } elseif ($action == 'getPeers') {
    $connections = getall("SELECT c.id, CONCAT(c.firstname, ' ', c.surname) AS name, COUNT(*) AS scenarios FROM pgrel a INNER JOIN pgrel b ON a.game_id = b.game_id AND a.title_id = 1 AND b.title_id = 1 INNER JOIN person c ON b.person_id = c.id WHERE a.person_id = $author_id AND b.person_id != $author_id GROUP BY c.id", FALSE);
    $result = ['result' => 'peers', 'id' => $author_id, 'name' => $name, 'connections' => $connections];
  }
}

if ($scenario_id) {
  $connections = getall("SELECT p.id, CONCAT(firstname, ' ', surname) AS name FROM pgrel INNER JOIN person p ON pgrel.person_id = p.id AND pgrel.title_id = 1 WHERE pgrel.game_id = $scenario_id", FALSE);

  $result = ['result' => 'authors', 'id' => $scenario_id, 'title' => $title, 'connections' => $connections];
}


print json_encode($result);
