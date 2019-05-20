<?php
require("./connect.php");
require("base.inc");
require("template.inc");

$backurl = "/gfx/corner-sys";

$result = getall("
	SELECT id, name
	FROM sys
	ORDER BY name, id
");

$list = "";

foreach($result AS $r) {
	$list .= "<a href=\"data?system={$r['id']}\">".htmlspecialchars($r['name'])."</a><br />\n";
}

// Smarty
$t->assign('list',$list);

$t->display('systems.tpl');

?>
