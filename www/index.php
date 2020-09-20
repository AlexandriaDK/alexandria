<?php
require("connect.php");
require("base.inc.php");

if (isset($_SESSION['login_after_redirect']) && isset($_SESSION['do_redirect']) ) { // assume valid URL
	header("Location: " . $_SESSION['login_after_redirect']);
	unset($_SESSION['login_after_redirect']);
	unset($_SESSION['do_redirect']);
	exit;
}

// fetching news
$newslist = [];
$i = 0;
foreach(getnews(10) AS $data) {
	$newslist[$i]['anchor'] = "news_".str_replace(array("-",":"," "),"",$data['published'])."_".$data['id'];
	$newslist[$i]['date'] = fulldate( $data['published'] );
	$newslist[$i]['news'] = textlinks($data['text']);
	$i++;
}

// for admins
$recentlog = [];
#if (isset($_SESSION['user_editor']) && $_SESSION['user_editor'] ) {
if ($_SESSION['user_editor'] ?? FALSE) {
	$recentlog = getrecentlog(10);
	$translations = getTranslationOverview();
}

// fetching latest scenarios for download
$latest_downloads = [];
$i = 0;
$files = getall("SELECT sce.id, sce.title, COALESCE(alias.label, sce.title) AS title_translation
	FROM sce
	INNER JOIN files ON sce.id = files.data_id AND files.category = 'sce'
	LEFT JOIN alias ON sce.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE files.downloadable = 1 AND sce.boardgame != 1
	GROUP BY sce.id
	ORDER BY MIN(files.inserted) DESC
	LIMIT 40
");

foreach($files AS $file) {
	$latest_downloads[$i]['id'] = $file['id'];
	$latest_downloads[$i]['title'] = $file['title_translation'];
	$latest_downloads[$i]['origtitle'] = $file['title'];
	$i++;
}

$scenarios_downloadable = getone("SELECT COUNT(DISTINCT data_id) FROM files WHERE category = 'sce' AND downloadable = 1");

$t->assign('type', 'front');
$t->assign('recentlog', $recentlog);
$t->assign('translations', $translations);
$t->assign('newslist', $newslist);
$t->assign('scenarios_downloadable', $scenarios_downloadable);
$t->assign('html_nextevents', getnexteventstable() );
$t->assign('latest_downloads', $latest_downloads);

ob_start();
$t->display('frontpage.tpl');
exit;

?>
