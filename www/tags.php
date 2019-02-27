<?php
require("./connect.php");
require("base.inc");
require("template.inc");

$order = ($_SERVER['QUERY_STRING'] == "popular" || isset($_REQUEST['popular']) ? "count desc" : "tag");

$totaltags = getone("SELECT COUNT(*) FROM tags");
$articles = getcol("SELECT tag FROM tag");

$tags = getall("
	SELECT COUNT(*) AS count, tag
	FROM tags
	GROUP BY tag
	ORDER BY $order
");

$list = "";

foreach($tags AS $tag) {
	$htmltag = "";
	$htmltag = "<a href=\"/data?tag=" . rawurlencode($tag['tag']) . "\">".htmlspecialchars($tag['tag'])."</a>";
	if (in_array($tag['tag'], $articles) ) {
		$htmltag = "<b>" . $htmltag . "</b>";
	}
	$htmltag .= " (" . $tag['count'] . ")";
	$htmltag .= "<br />\n";
	$list .= $htmltag;
}

// Smarty
$t->assign('list',$list);

$t->display('tags.tpl');

?>
