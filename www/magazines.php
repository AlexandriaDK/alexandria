<?php
require("./connect.php");
require("base.inc.php");

$this_type = 'issue';

$id = NULL;
$magazineid = (int) ($_GET['id'] ?? 0);
$issueid = (int) ($_GET['issue'] ?? 0);
$error = FALSE;
$magazinename = $magazinedescription = '';
$issue = $issues = $articles = $colophon = $arrows = [];
$available_pic = $picpath = $picid = FALSE;
$internal = '';
$filelist = [];

if ($magazineid) {
	$id = $magazineid;
	list($magazinename, $magazinedescription, $internal) = getrow("SELECT name, description, internal FROM magazine WHERE id = $magazineid");
	$internal = ( ( $_SESSION['user_editor'] ?? FALSE ) ? $internal : ''); // only set internal if editor
	if (! $magazinename) {
		$error = TRUE;
	} else {
		$issues = getall("SELECT id, title, releasedate, releasetext FROM issue WHERE magazine_id = $magazineid ORDER BY releasedate, id");
		foreach ($issues AS $key => $issue) {
			$issues[$key]['thumbnail'] = hasthumbnailpic($issue['id'], 'issue');
		}
		#var_dump($issues);
		#exit;
	}
} elseif ($issueid) {
	$id = $issueid;
	if ($available_pic = hasthumbnailpic($issueid, $this_type)) {
		$picpath = getcategorythumbdir('issue');
		$picid = $issueid;
	}
	$issue = getrow("
		SELECT issue.title, issue.releasetext, issue.internal, magazine.id AS magazineid, magazine.name AS magazinename
		FROM issue
		INNER JOIN magazine ON issue.magazine_id = magazine.id
		WHERE issue.id = $issueid
	");
	$internal = ( ( $_SESSION['user_editor'] ?? FALSE ) ? $issue['internal'] : ''); // only set internal if editor
	// two lookups with and without page being NULL could be combined to one
	// No need to create article tree with authors as subset. Template already handles that.
	$colophon = getall("
		SELECT article.id, contributor.aut_id, contributor.aut_extra, contributor.role, article.page, article.title, article.description, article.articletype, article.sce_id, CONCAT(aut.firstname, ' ', aut.surname) AS name, sce.title AS scetitle
		FROM article
		LEFT JOIN contributor ON article.id = contributor.article_id
		LEFT JOIN aut ON contributor.aut_id = aut.id
		LEFT JOIN sce ON article.sce_id = sce.id
		WHERE issue_id = $issueid
		AND page IS NULL AND article.title = ''
		ORDER BY article.id
	");
	$articles = getall("
		SELECT article.id, contributor.aut_id, contributor.aut_extra, contributor.role, article.page, article.title, article.description, article.articletype, article.sce_id, CONCAT(aut.firstname, ' ', aut.surname) AS name, sce.title AS scetitle
		FROM article
		LEFT JOIN contributor ON article.id = contributor.article_id
		LEFT JOIN aut ON contributor.aut_id = aut.id
		LEFT JOIN sce ON article.sce_id = sce.id
		WHERE issue_id = $issueid
		AND (page IS NOT NULL OR article.title != '')
		ORDER BY page, article.id
	");

	$issues = getall("SELECT id, title, releasedate, releasetext FROM issue WHERE magazine_id = " . $issue['magazineid'] . " ORDER BY releasedate, id");
	$seriecount = 0;
	$seriedata = [];
	$seriethis = FALSE;
	foreach($issues AS $row) {
		$seriecount++;
		$seriedata[$seriecount]['id'] = $row['id'];
		$seriedata[$seriecount]['title'] = $row['title'];
		$seriedata[$seriecount]['releasetext'] = $row['releasetext'];
		if ($row['id'] == $issueid) $seriethis = $seriecount;
	
		if ($seriethis) {
			if (isset($seriedata[($seriethis-1)])) {
				$arrows['prev'] = $seriedata[($seriethis-1)];
				$arrows['prev']['active'] = TRUE;
			} else {
				$arrows['prev']['active'] = FALSE;
			}
			if (isset($seriedata[($seriethis+1)])) {
				$arrows['next'] = $seriedata[($seriethis+1)];
				$arrows['next']['active'] = TRUE;
			} else {
				$arrows['next']['active'] = FALSE;
			}
		}
	}
	// List of files
	$filelist = getfilelist($issueid,$this_type);

} else {
	$magazines = getall("
		SELECT magazine.id, magazine.name, magazine.description, COUNT(issue.id) AS issuecount
		FROM magazine
		LEFT JOIN issue ON issue.magazine_id = magazine.id
		GROUP BY magazine.id, magazine.name
		ORDER BY magazine.name
	");
}

// Smarty
$t->assign('magazineid',$magazineid);
$t->assign('issueid',$issueid);
$t->assign('id',$id);
$t->assign('magazines',$magazines);
$t->assign('magazinename',$magazinename);
$t->assign('magazinedescription',$magazinedescription);
$t->assign('intern',$internal);
$t->assign('issues',$issues);
$t->assign('issue',$issue);
$t->assign('colophon',$colophon);
$t->assign('articles',$articles);
$t->assign('error', $error);
$t->assign('pic',$available_pic);
$t->assign('picpath',$picpath);
$t->assign('picid',$picid);
$t->assign('arrowset',$arrows);
// $t->assign('ogimage', getimageifexists($con, 'convent') );
$t->assign('filelist',$filelist);
$t->assign('filedir', getcategorydir($this_type) );

$t->display('magazines.tpl');

?>
