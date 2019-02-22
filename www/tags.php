<?php
require("./connect.php");
require("base.inc");
require("template.inc");

$tags = getcol("
	SELECT DISTINCT tag
	FROM tags
	ORDER BY tag
");

$list = "";

foreach($tags AS $tag) {
	$list .= "<a href=\"/data?tag=" . rawurlencode($tag) . "\">".htmlspecialchars($tag)."</a><br />\n";
}

// Smarty
$t->assign('list',$list);

$t->display('tags.tpl');

?>
