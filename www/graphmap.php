<?php
require("./connect.php");
require_once("base.inc");

// people
$people = getcol("SELECT CONCAT(firstname, ' ', surname) AS id_name FROM aut ORDER BY firstname, surname");	
$json_people = json_encode($people);

$t->assign('type','jostgame');
$t->assign('json_people', $json_people );

$t->display('graphmap.tpl');

?>
