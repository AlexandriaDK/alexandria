<?php
require_once "./connect.php";
require_once "base.inc.php";

$name = (string) $_REQUEST['name'];
$auto = (string) $_REQUEST['auto'];
$start = (string) $_REQUEST['start'];

// people
$people = getcol("SELECT CONCAT(firstname, ' ', surname) AS id_name FROM aut ORDER BY firstname, surname");
$json_people = json_encode($people);

$t->assign('type', 'jostgame');
$t->assign('json_people', $json_people);
$t->assign('name', $name);
$t->assign('auto', $auto);
$t->assign('start', $start);

$t->display('graphmap.tpl');
