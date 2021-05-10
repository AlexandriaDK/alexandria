<?php
require("./connect.php");
require("base.inc.php");

$magazineid = (int) ($_GET['id'] ?? 0);
$issueid = (int) ($_GET['issue'] ?? 0);
$error = FALSE;
$magazinename = '';
$issue = [];
$issues = [];
$articles = [];
$colophone = [];

if ($magazineid) {
	$magazinename = getone("SELECT name FROM magazine WHERE id = $magazineid");
	if (! $magazinename) {
		$error = TRUE;
	} else {
		$issues = getall("SELECT id, title, releasedate, releasetext FROM issue WHERE magazine_id = $magazineid ORDER BY releasedate, title, id");
	}
} elseif ($issueid) {
	$issue = getrow("
		SELECT issue.title, issue.releasetext, magazine.id AS magazineid, magazine.name AS magazinename
		FROM issue
		INNER JOIN magazine ON issue.magazine_id = magazine.id
		WHERE issue.id = $issueid
	");
	// two lookups with and without page being NULL could be combined to one
	$colophone = getall("
		SELECT aut_id, aut_extra, role, page, title, description, articletype, CONCAT(aut.firstname, ' ', aut.surname) AS name
		FROM airel
		LEFT JOIN aut ON airel.aut_id = aut.id
		WHERE issue_id = $issueid
		AND page IS NULL
		ORDER BY airel.id
	");
	$articles = getall("
		SELECT airel.aut_id, airel.aut_extra, airel.role, airel.page, airel.title, airel.description, airel.articletype, airel.sce_id, CONCAT(aut.firstname, ' ', aut.surname) AS name, sce.title AS scetitle
		FROM airel
		LEFT JOIN aut ON airel.aut_id = aut.id
		LEFT JOIN sce ON airel.sce_id = sce.id
		WHERE issue_id = $issueid
		AND page IS NOT NULL
		ORDER BY page, airel.id
	");
} else {
	$magazines = getall("
		SELECT airel.aut_id, airel.aut_extra, airel.role, airel.page, airel.title, airel.description, airel.articletype, CONCAT(aut.firstname, ' ', aut.surname) AS name, sce.title AS scetitle
		FROM airel
		LEFT JOIN aut ON airel.aut_id = aut.id
		LEFT JOIN sce ON airel.sce_id = sce.id
		GROUP BY magazine.id, magazine.name
		ORDER BY magazine.name
	");
}

// Smarty
$t->assign('magazineid',$magazineid);
$t->assign('issueid',$issueid);
$t->assign('magazines',$magazines);
$t->assign('magazinename',$magazinename);
$t->assign('issues',$issues);
$t->assign('issue',$issue);
$t->assign('colophone',$colophone);
$t->assign('articles',$articles);
$t->assign('error', $error);
$t->display('magazines.tpl');
?>
