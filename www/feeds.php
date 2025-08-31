<?php
require("./connect.php");
require("base.inc.php");

$articles = getall("SELECT a.owner, a.name, a.person_id, a.podcast, b.title, b.link, b.pubdate, b.comments FROM feeds a, feedcontent b WHERE a.id = b.feed_id ORDER BY b.pubdate DESC LIMIT 0,40");

$feeddata = array();
$feedlist = array();

foreach ($articles as $id => $article) {
  $feeddata[$id] = $article;
  if ($feeddata[$id]['title'] == "") {
  }

  $feeddata[$id]['printdate'] = pubdateprint($article['pubdate']);
}

foreach (getall("SELECT owner, name, pageurl FROM feeds WHERE pauseupdate = 0 ORDER BY owner") as $id => $data) {
  $feedlist[$id] = $data;
}

award_achievement(61);

$t->assign('feeddata', $feeddata);
$t->assign('feedlist', $feedlist);

$t->display('feeds.tpl');
