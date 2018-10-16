<?php
// Viderestil evt. til adm-delen
if (isset($_SERVER['SERVER_NAME']) && in_array(strtolower($_SERVER['SERVER_NAME']), ['adm.alexandria.dk','www.adm.alexandria.dk'] )) {
	header("Location: /adm/");
	exit;
}

require("./connect.php");
require("base.inc");
require("template.inc");

// fetching news
$newslist = array();
$i = 0;
foreach(getnews() AS $data) {
	$newslist[$i]['anchor'] = "news_".str_replace(array("-",":"," "),"",$data['published'])."_".$data['id'];
	$newslist[$i]['date'] = nicedateset($data['published'],$data['published']);
	$newslist[$i]['news'] = textlinks($data['text']);
	$i++;
	if ($i >= 10) break;
}

// for admins
$recentlog = [];
if (isset($_SESSION['user_editor']) && $_SESSION['user_editor'] ) {
	$recentlog = getrecentlog(3);
}

// fetching latest scenarios for download
$latest_downloads = array();
$i = 0;
$files = getall("SELECT a.id, a.title FROM sce a, files b WHERE a.id = b.data_id AND b.category = 'sce' AND downloadable = 1 AND a.boardgame != 1 GROUP BY a.id ORDER BY b.inserted DESC LIMIT 20");
foreach($files AS $file) {
	$latest_downloads[$i]['id'] = $file['id'];
	$latest_downloads[$i]['title'] = $file['title'];
	$i++;
}


$scenarios_downloadable = getone("SELECT COUNT(DISTINCT data_id) FROM files WHERE category = 'sce' AND downloadable = 1");

$t->assign('type','front');
$t->assign('recentlog',$recentlog);
$t->assign('newslist',$newslist);
$t->assign('scenarios_downloadable',$scenarios_downloadable);
$t->assign('html_nextevents',getnexteventstable());
$t->assign('latest_downloads',$latest_downloads);

ob_start();
$t->display('frontpage.pb.tpl');
//skinke($t->fetch('frontpage.tpl'));
exit;

?>
