<?php
require_once "./connect.php";
require_once "base.inc.php";

// skal laves om til en funktion
$value = "";

$data_id = (int) ($_REQUEST['data_id'] ?? false);
$cat = (string) ($_REQUEST['cat'] ?? false);
$label = getentry($cat, $data_id);

$t->assign('category', $cat);
$t->assign('data_id', $data_id);
$t->assign('label', $label);
$t->display('update_input.tpl');
