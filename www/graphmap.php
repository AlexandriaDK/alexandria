<?php
require("./connect.php");
require_once("base.inc");
require_once("template.inc");
require_once("smartfind.inc");

$content = "";

$content .= '<form action="" method="get" onsubmit="return getAuthorGraph();">';
$content .= "Indtast forfatter: <input type=\"text\" name=\"authorinput\" id=\"authorinput\" class=\"tags\" value=\"\" />".($from_error?' <span class="finderror">?</span> ':'')."</td></tr>\n";
$content .= "</form>";


// people
$people = getcol("SELECT CONCAT(firstname, ' ', surname) AS id_name FROM aut ORDER BY firstname, surname");	
$json_people = json_encode($people);

$t->assign('type','jostgame');
$t->assign('content',$content);
$t->assign('json_people', $json_people );

$t->display('graphmap.tpl');

?>
