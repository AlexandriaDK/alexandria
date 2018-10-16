<?php
/*
 * Idé til aprilsnar:
 *
 * Oversæt tekststykker og sætninger ved onmouseover
 *
 * */

// Viderestil evt. til adm-delen
if (in_array(strtolower($_SERVER['SERVER_NAME']), array('adm.alexandria.dk','www.adm.alexandria.dk') )) {
	header("Location: http://alexandria.dk/adm/");
	exit;
}

require("./connect.php");
require("base.inc");
require("template.inc");

// fetching news
$newslist = array();
$i = 0;
foreach(getnews() AS $data) {
	$newslist[$i]['anchor'] = "news_".str_replace("-","",$data['published'])."_".$data['id'];
	$newslist[$i]['date'] = nicedateset($data['published'],$data['published']);
	$newslist[$i]['news'] = textlinks($data['text']);
	$i++;
	if ($i >= 10) break;
}

// fetching latest scenarios for download
$latest_downloads = array();
$i = 0;
$files = getall("SELECT a.id, a.title FROM sce a, files b WHERE a.id = b.data_id AND b.category = 'sce' AND downloadable = 1 GROUP BY a.id ORDER BY b.inserted DESC LIMIT 15");
foreach($files AS $file) {
	$latest_downloads[$i]['id'] = $file['id'];
	$latest_downloads[$i]['title'] = $file['title'];
	$i++;
}


$scenarios_downloadable = getone("SELECT COUNT(DISTINCT data_id) FROM files WHERE category = 'sce' AND downloadable = 1");

$t->assign('type','front');
$t->assign('newslist',$newslist);
$t->assign('scenarios_downloadable',$scenarios_downloadable);
$t->assign('latest_downloads',$latest_downloads);

ob_start();
$t->display('frontpage.afd.2009.tpl');
//skinke($t->fetch('frontpage.tpl'));
exit;

?>
