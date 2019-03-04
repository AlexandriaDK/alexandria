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

$taglist = [];
foreach($tags AS $tag) {
	$url = "/data?tag=" . rawurlencode($tag['tag']);
	$tagname = $tag['tag'];
	$has_article = in_array($tag['tag'], $articles);
	$count = $tag['count'];
	$dataset = ['url' => $url, 'tagname' => $tagname, 'has_article' => $has_article, 'count' => $count];
	$taglist[] = $dataset;

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
$t->assign('taglist',$taglist);

$t->display('tags.tpl');

?>
