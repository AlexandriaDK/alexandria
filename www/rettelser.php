<?php
require("./connect.php");
require("base.inc");
require("template.inc");

// skal laves om til en funktion
$value = "";

$data_id = (int) $_REQUEST['data_id'];
$cat = (string) $_REQUEST['cat'];
$label = getentry($cat,$data_id);

$t->assign('category',$cat);
$t->assign('data_id',$data_id);
$t->assign('label',$label);
$t->display('update_input.tpl');
?>
