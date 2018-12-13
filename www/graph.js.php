<?php
require("./connect.php");
require_once("base.inc");

$name = (string) $_REQUEST['name'];
$action = (string) $_REQUEST['action'];
$author_id = (int) $_REQUEST['author_id'];
$scenario_id = (int) $_REQUEST['scenario_id'];
$title = (string) $_REQUEST['title'];

if ($name) {
	list($author_id, $name) = getrow("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM aut WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($name) ."'");

	if (!$author_id) {
		die("Error");
	}
}
if ($author_id && !$name) {
	$name = getone("SELECT CONCAT(firstname, ' ', surname) AS name FROM aut WHERE id = $author_id");
}

if ($title) {
	list($scenario_id, $title) = getrow("SELECT id, title FROM sce WHERE title = '" . dbesc($title) ."' LIMIT 1");

	if (!$scenario_id) {
		die("Error");
	}

}

if ($scenario_id && !$title) {
	$title = getone("SELECT title FROM sce WHERE id = $scenario_id");
}

if ($author_id) {
	if ($action == 'getScenarios') {
		$connections = getall("SELECT sce.id, title FROM asrel INNER JOIN sce ON asrel.sce_id = sce.id AND asrel.tit_id = 1 WHERE asrel.aut_id = $author_id", FALSE);
		$result = ['result' => 'scenarios', 'id' => $author_id, 'name' => $name, 'connections' => $connections];
	} elseif ($action == 'getPeers') {
		$connections = getall("SELECT c.id, CONCAT(c.firstname, ' ', c.surname) AS name, COUNT(*) AS scenarios FROM asrel a INNER JOIN asrel b ON a.sce_id = b.sce_id AND a.tit_id = 1 AND b.tit_id = 1 INNER JOIN aut c ON b.aut_id = c.id WHERE a.aut_id = $author_id AND b.aut_id != $author_id GROUP BY c.id", FALSE);
		$result = ['result' => 'peers', 'id' => $author_id, 'name' => $name, 'connections' => $connections];
	}
}

if ($scenario_id) {
	$connections = getall("SELECT aut.id, CONCAT(firstname, ' ', surname) AS name FROM asrel INNER JOIN aut ON asrel.aut_id = aut.id AND asrel.tit_id = 1 WHERE asrel.sce_id = $scenario_id", FALSE);

	$result = ['result' => 'authors', 'id' => $scenario_id, 'title' => $title, 'connections' => $connections];
}


print json_encode($result);
?>
