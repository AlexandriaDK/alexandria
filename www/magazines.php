<?php
require("./connect.php");
require("base.inc.php");

$this_type = 'issue';

$magazineid = (int) ($_GET['id'] ?? 0);
$issueid = (int) ($_GET['issue'] ?? 0);
$error = FALSE;
$magazinename = $magazinedescription = '';
$issue = $issues = $articles = $colophone = $arrows = [];
$available_pic = $picpath = $picid = FALSE;
$internal = '';

if ($magazineid) {
	list($magazinename, $magazinedescription, $internal) = getrow("SELECT name, description, internal FROM magazine WHERE id = $magazineid");
	$internal = ( ( $_SESSION['user_editor'] ?? FALSE ) ? $internal : ''); // only set internal if editor
	if (! $magazinename) {
		$error = TRUE;
	} else {
		$issues = getall("SELECT id, title, releasedate, releasetext FROM issue WHERE magazine_id = $magazineid ORDER BY releasedate, id");
	}
} elseif ($issueid) {
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
	$colophone = getall("
		SELECT airel.aut_id, airel.aut_extra, airel.role, airel.page, airel.title, airel.description, airel.articletype, CONCAT(aut.firstname, ' ', aut.surname) AS name, sce.title AS scetitle
		FROM airel
		LEFT JOIN aut ON airel.aut_id = aut.id
		LEFT JOIN sce ON airel.sce_id = sce.id
		WHERE issue_id = $issueid
		AND page IS NULL AND airel.title = ''
		ORDER BY airel.id
		");
		$articles = getall("
		SELECT airel.aut_id, airel.aut_extra, airel.role, airel.page, airel.title, airel.description, airel.articletype, airel.sce_id, CONCAT(aut.firstname, ' ', aut.surname) AS name, sce.title AS scetitle
		FROM airel
		LEFT JOIN aut ON airel.aut_id = aut.id
		LEFT JOIN sce ON airel.sce_id = sce.id
		WHERE issue_id = $issueid
		AND (page IS NOT NULL OR airel.title != '')
		ORDER BY page, airel.id
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
$t->assign('magazines',$magazines);
$t->assign('magazinename',$magazinename);
$t->assign('magazinedescription',$magazinedescription);
$t->assign('intern',$internal);
$t->assign('issues',$issues);
$t->assign('issue',$issue);
$t->assign('colophone',$colophone);
$t->assign('articles',$articles);
$t->assign('error', $error);
$t->assign('pic',$available_pic);
$t->assign('picpath',$picpath);
$t->assign('picid',$picid);
$t->assign('arrowset',$arrows);
// $t->assign('ogimage', getimageifexists($con, 'convent') );
$t->display('magazines.tpl');
?>
