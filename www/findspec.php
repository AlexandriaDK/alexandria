<?php
require("./connect.php");
require("base.inc");

$systems = getall("SELECT id, name FROM sys ORDER BY name");
$genres = getall("SELECT id, name FROM gen WHERE genre = 1 ORDER BY name");
$categories = getall("SELECT id, name FROM gen WHERE genre = 0 ORDER BY name");
$consets = getall("SELECT id, name FROM conset ORDER BY name");

$t->assign('servername',$_SERVER['SERVER_NAME']);
$t->assign('systems',$systems);
$t->assign('genres',$genres);
$t->assign('categories',$categories);
$t->assign('consets',$consets);
$t->display('find_advanced.tpl');
?>
