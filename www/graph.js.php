<?php
require("./connect.php");
require_once("base.inc");

$name = (string) $_REQUEST['name'];

list($id, $name) = getrow("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM aut WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($name) ."'");

if (!$id) {
	die("Error");
}

$connections = getall("SELECT sce.id, title FROM asrel INNER JOIN sce ON asrel.sce_id = sce.id AND asrel.tit_id = 1 WHERE asrel.aut_id = $id", FALSE);

$result = ['id' => $id, 'name' => $name, 'connections' => $connections];
print json_encode($result);
?>
