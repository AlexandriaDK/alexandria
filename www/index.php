<?php
require_once "connect.php";
require_once "base.inc.php";

if (isset($_SESSION['login_after_redirect']) && isset($_SESSION['do_redirect'])) { // assume valid URL
  header("Location: " . $_SESSION['login_after_redirect']);
  unset($_SESSION['login_after_redirect']);
  unset($_SESSION['do_redirect']);
  exit;
}

// fetching news
$newslist = [];
$i = 0;
foreach (getnews(10) as $data) {
  $newslist[$i]['anchor'] = "news_" . str_replace(array("-", ":", " "), "", $data['published']) . "_" . $data['id'];
  $newslist[$i]['date'] = fulldate($data['published']);
  $newslist[$i]['news'] = textlinks($data['text']);
  $i++;
}

// for admins
$recentlog = $translations = [];
#if (isset($_SESSION['user_editor']) && $_SESSION['user_editor'] ) {
if ($_SESSION['user_editor'] ?? false) {
  $recentlog = getrecentlog(10);
  $translations = getTranslationOverview();
}

// fetching latest scenarios for download
$latest_downloads = [];
$i = 0;
$files = getLatestFiles(40);

foreach ($files as $file) {
  $latest_downloads[$i]['id'] = $file['id'];
  $latest_downloads[$i]['title'] = $file['title_translation'];
  $latest_downloads[$i]['origtitle'] = $file['title'];
  $i++;
}

$scenarios_downloadable = getone("SELECT COUNT(DISTINCT game_id) FROM files WHERE downloadable = 1");
$nextevents = getnexteventstable();

$t->assign('type', 'front');
$t->assign('recentlog', $recentlog);
$t->assign('translations', $translations);
$t->assign('newslist', $newslist);
$t->assign('scenarios_downloadable', $scenarios_downloadable);
$t->assign('html_nextevents', $nextevents);
$t->assign('latest_downloads', $latest_downloads);

ob_start();

$t->display('frontpage.tpl');
exit;
