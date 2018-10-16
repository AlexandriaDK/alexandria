<?php
require("./connect.php");
require("base.inc");
require("template.inc");

$articles = getall("SELECT a.owner, a.name, a.aut_id, b.title, b.link, b.pubdate, b.comments FROM feeds a, feedcontent b WHERE a.id = b.feed_id ORDER BY b.pubdate DESC LIMIT 0,40");

$feeddata = array();
$feedlist = array();

foreach($articles AS $id => $article) {
	$feeddata[$id] = $article;
	if ($feeddata[$id]['title'] == "") {
//		$feeddata[$id]['title'] = "(uden titel)";
	}
	
	$feeddata[$id]['printdate'] = pubdateprint($article['pubdate']);

/*
	$title = (strlen($article['title']) > 40 ? substr($article['title'],0,40)."...":$article['title']);
	if ($title === "") {
		$title = "(ingen titel)";
	}
	$content .= '<tr>';
	$content .= '<td title="'.htmlspecialchars($article['title']).'"><a href="'.htmlspecialchars($article['link']).'">'.htmlspecialchars($title).'</a></td>';
	$content .= '<td>'.htmlspecialchars($article['owner']).'</td>';
	$content .= '<td>'.htmlspecialchars($article['pubdate']).'</td>';
	$content .= '</tr>';
	$content .= "\n";
*/
}

foreach(getall("SELECT owner, name, pageurl FROM feeds ORDER BY owner") AS $id => $data) {
	$feedlist[$id] = $data;
}

award_achievement(61);

$t->assign('feeddata',$feeddata);
$t->assign('feedlist',$feedlist);

$t->display('feeds.tpl');

?>
