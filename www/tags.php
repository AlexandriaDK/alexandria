<?php
require("./connect.php");
require("base.inc.php");

$order = ($_SERVER['QUERY_STRING'] == "popular" || isset($_REQUEST['popular']) ? "count desc" : "tag");

$totaltags = getone("SELECT COUNT(*) FROM tags");
$articles = getcol("SELECT tag FROM tag");

$tags = getall("
	SELECT COUNT(tags.id) AS count, alltags.tag
	FROM (
		SELECT DISTINCT tag FROM tags
		UNION
		SELECT tag FROM tag
	) alltags
	LEFT JOIN tags ON alltags.tag = tags.tag
	GROUP BY alltags.tag
	ORDER BY $order
");

$list = "";

$taglist = [];
foreach($tags AS $tag) {
	$url = "data?tag=" . rawurlencode($tag['tag']);
	$tagname = $tag['tag'];
	$has_article = in_array($tag['tag'], $articles);
	$count = $tag['count'];
	$dataset = ['url' => $url, 'tagname' => $tagname, 'has_article' => $has_article, 'count' => $count];
	$taglist[] = $dataset;
}

// Smarty
$t->assign('taglist',$taglist);

$t->display('tags.tpl');

?>
