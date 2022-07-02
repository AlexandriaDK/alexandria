<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$this_type = 'magazine';

$action = (string) ($_REQUEST['action'] ?? '');
$do = (string) ($_REQUEST['do'] ?? '');
$description = trim((string) ($_REQUEST['description'] ?? '') );
$internal = (string) ($_REQUEST['internal'] ?? '');
$name = trim((string) ($_REQUEST['name'] ?? '') );
$title = trim((string) ($_REQUEST['title'] ?? '') );
$releasedate = (string) ($_REQUEST['releasedate'] ?? '');
$releasetext = (string) ($_REQUEST['releasetext'] ?? '');
$status = (int) ($_REQUEST['status'] ?? '');
$magazine_id = (int) ($_REQUEST['magazine_id'] ?? '');
$issue_id = (int) ($_REQUEST['issue_id'] ?? '');
$article_id = (int) ($_REQUEST['article_id'] ?? '');
$highlight_article_id = (int) ($_SESSION['highlight_article_id'] ?? '');
$page = (int) ($_REQUEST['page'] ?? '');
$articletype = trim((string) ($_REQUEST['articletype'] ?? '') );
$game_id = (int) ($_REQUEST['game_id'] ?? '');
$contributors = (array) ($_REQUEST['contributors'] ?? []);
$references = (array) ($_REQUEST['references'] ?? []);
$original_article_id = (int) ($_REQUEST['original_article_id'] ?? '');
unset($_SESSION['highlight_article_id']);

$statuslist = [
	  0 => [ 'label' => 'notstarted', 'text' => 'Not started', 'short' => 'No'],
	//  20 => [ 'label' => 'issueuploaded', 'text' => 'Issue uploaded'],
	 40 => [ 'label' => 'wip', 'text' => 'Work in progress', 'short' => 'WIP'],
	 60 => [ 'label' => 'almostfinished', 'text' => 'Finished (missing small parts - please write comment)', 'short' => 'Almost'],
	 80 => [ 'label' => 'finishednorefcon', 'text' => 'Finished (not checked for references or cons+events)', 'short' => 'Finished (-ref, -con)'],
	 85 => [ 'label' => 'finishednoref', 'text' => 'Finished (not checked for references) ', 'short' => 'Finished (-ref)'],
	 90 => [ 'label' => 'finishednocon', 'text' => 'Finished (not checked for cons+events)', 'short' => 'Finished (-con)'],
	 95 => [ 'label' => 'finished', 'text' => 'Finished (and checked)', 'short' => 'Finished'],
	100 => [ 'label' => 'finishedpublished', 'text' => 'Finished and published', 'short' => 'Published'],
];

function insertContributors($contributors, $article_id) {
	doquery("DELETE FROM contributor WHERE article_id = $article_id");
	foreach ($contributors AS $contributor) {
		$role = trim($contributor['role']);
		if ($contributor['person'] == '' && $role == '') {
			continue;
		}
		$person = autidextra($contributor['person']);
		doquery("
			INSERT INTO contributor (person_id, person_extra, role, article_id)
			VALUES (" . sqlifnull($person['id']) . ", '" . dbesc($person['extra']) . "', '" . dbesc($role) . "', $article_id)
		");
	}
}

function insertReferences($references, $article_id) {
	doquery("DELETE FROM article_reference WHERE article_id = $article_id");
	$match = '_^(c|cs|tag|sys|p|m|i|g)(\d+)_';
	foreach ($references AS $reference) {
		if (! preg_match($match, $reference, $matches)) {
			continue;
		}
		doquery("
			INSERT INTO article_reference (article_id, category, data_id)
			VALUES ($article_id, '" . dbesc(getCategoryFromShort($matches[1])) . "', " . (int) $matches[2] . ")
		");
	}
}

if ($issue_id && ! $magazine_id) {
	$magazine_id = getone("SELECT magazine_id FROM issue WHERE id = $issue_id");
}

// Magazines
if ($action == "changemagazine" && $do != "Delete") {
	$q = "UPDATE magazine SET " .
	     "name = '" . dbesc($name) . "', " .
	     "description = '" . dbesc($description) . "', " .
	     "internal = '" . dbesc($internal) . "' " .
	     "WHERE id = $magazine_id";
	$r = doquery($q);
	if ($r) {
		chlog($magazine_id,$this_type,"Magazine updated: $magazine_id - $name");
	}
	$_SESSION['admin']['info'] = "Magazine updated! " . dberror();
	rexit($this_type);
}

if ($action == "changemagazine" && $do == "Delete") {
	// Only delete if no issues
	$q = "SELECT COUNT(*) FROM issue where magazine_id = $magazine_id";
	$r = getone($q);
	if($r != 0) {
		$_SESSION['admin']['info'] = "The magazine needs to have no issues before it can be removed! " . dberror();
		rexit($this_type);
	}

	$q = "DELETE FROM magazine WHERE id = $magazine_id";
	$r = doquery($q);
	if ($r) {
		chlog($magazine_id,$this_type,"Magazine removed: $id");
	}
	$_SESSION['admin']['info'] = "Magazine removed! " . dberror();
	rexit($this_type);
}

if ($action == "addmagazine") {
	$q = "INSERT INTO magazine " .
	     "(name, description, internal) VALUES ".
	     "('" . dbesc($name) . "', '" . dbesc($description) . "', '" . dbesc($internal) . "')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($id,$this_type,"Magazine created: $name");
	}
	$_SESSION['admin']['info'] = "Magazine created! " . dberror();
	rexit($this_type);
}

// Issues
if ($action == "changeissue" && $do != "Delete") {
	$q = "UPDATE issue SET " .
	     "title = '" . dbesc($title) . "', " .
	     "releasedate = " . sqlifnull($releasedate) . ", " .
	     "releasetext = '" . dbesc($releasetext) . "', " .
	     "status = '" . $status . "', " .
	     "internal = '" . dbesc($internal) . "' " .
	     "WHERE id = $issue_id";
	$r = doquery($q);
	if ($r) {
		chlog($issue_id,'issue',"Issue updated");
	}
	$_SESSION['admin']['info'] = "Issue updated! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id]);
}

if ($action == "changeissue" && $do == "Delete") {
	// Only delete if no articles
	$q = "SELECT COUNT(*) FROM article where issue_id = $issue_id";
	$r = getone($q);
	if($r != 0) {
		$_SESSION['admin']['info'] = "The issue needs to have no articles before it can be removed! " . dberror();
		rexit($this_type, ['magazine_id' => $magazine_id]);
	}

	$q = "DELETE FROM issue WHERE id = $issue_id";
	$r = doquery($q);
	if ($r) {
		chlog($issue_id,'issue',"Issue removed: $id");
	}
	$_SESSION['admin']['info'] = "Issue removed! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id]);
}

if ($action == "addissue") {
	$q = "INSERT INTO issue " .
	     "(title, releasedate, releasetext, magazine_id, internal, status) VALUES ".
	     "('" . dbesc($title) . "', ". sqlifnull($releasedate) . ", '" . dbesc($releasetext) . "', $magazine_id, '" . dbesc($internal) . "', $status)";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($id,'issue',"Issue created: $title");
	}
	$_SESSION['admin']['info'] = "Issue created! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id]);
}

// Articles
if ($action == "changearticle" && $do != "Delete") {
	$q = "UPDATE article SET " .
	     "page = " . sqlifnull($page) . ", " .
	     "title = '" . dbesc($title) . "', " .
	     "description = '" . dbesc($description) . "', " .
	     "articletype = '" . dbesc($articletype) . "', " .
	     "game_id = " . sqlifnull($game_id) . " " .
	     "WHERE id = $article_id";
	$r = doquery($q);
	if ($r) {
		// Contributors
		insertContributors($contributors, $article_id);
		insertReferences($references, $article_id);
		chlog($issue_id,'issue',"Article updated: $article_id - $title");
	}
	$_SESSION['admin']['info'] = "Article $article_id updated! " . dberror();
	$_SESSION['highlight_article_id'] = $article_id;
	rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id]);
}

if ($action == "changearticle" && $do == "Delete") {
	doquery("DELETE FROM contributor WHERE article_id = $article_id");
	doquery("DELETE FROM article_reference WHERE article_id = $article_id");
	$q = "DELETE FROM article WHERE id = $article_id";
	$r = doquery($q);
	if ($r) {
		chlog($issue_id,'issue',"Article removed: $article_id");
	}
	$_SESSION['admin']['info'] = "Article removed! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id ]);
}

if ($action == "addarticle") {
	$contributor_count = $reference_count = 0;
	foreach($contributors as $contributor) {
		if ($contributor['person'] != "" || $contributor['role'] != "") {
			$contributor_count++;
		}
	}

	foreach($references as $reference) {
		if ($reference != "") {
			$reference_count++;
		}
	}

	if (
		! $page &&
		! $title &&
		! $description &&
		! $articletype &&
		! $game_id &&
		! $contributor_count &&
		! $reference_count
	) {
		$_SESSION['admin']['info'] = "No article data provided " . dberror();
		rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id ]);
	}
	$q = "INSERT INTO article " .
	     "(issue_id, page, title, description, articletype, game_id) VALUES ".
	     "($issue_id, ".  sqlifnull($page) . ", '" . dbesc($title) . "', '" . dbesc($description) . "', '" . dbesc($articletype) . "', " . sqlifnull($game_id) . ")";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		insertContributors($contributors, $id);
		insertReferences($references, $id);
		chlog($issue_id,'issue',"Article created: $id - $title");
	}
	$_SESSION['admin']['info'] = "Article $id created! " . dberror();
	$_SESSION['highlight_article_id'] = $id;
	rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id ]);
}

if ($action == "duplicatearticle" && $original_article_id && $issue_id) {
	$r = doquery("INSERT INTO article (issue_id, page, title, description, articletype, game_id) SELECT $issue_id, page, title, description, articletype, game_id FROM article WHERE id = $original_article_id");
	if ($r) {
		$new_article_id = dbid();
		doquery("INSERT INTO contributor (article_id, person_id, person_extra, role) SELECT $new_article_id, person_id, person_extra, role FROM contributor WHERE article_id = $original_article_id");
		chlog($issue_id,'issue',"Article duplicated: $original_article_id => $new_article_id");
	}
	$_SESSION['admin']['info'] = "Article duplicated! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id ]);

}
?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Magazines</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="/uistyle.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="adm.js"></script>
<script type="text/javascript">
$(function() {
	$( ".peopletags" ).autocomplete({
		source: 'lookup.php?type=person',
		autoFocus: true,
		delay: 50,
		minLength: 3
	});
	$( ".gametags" ).autocomplete({
		source: 'lookup.php?type=game',
		autoFocus: true,
		delay: 50,
		minLength: 3
	});
	var peopleRoles = ['Skribent', 'Illustrator', 'Fotograf', 'Redaktør', 'Chefredaktør', 'Redaktion', 'Layout', 'Forfatter', 'Tegner', 'Anmelder', 'Brevkasseredaktør', 'Ansvarshavende redaktør', 'Lokalredaktion - Århus']
	$( ".peopleroles" ).autocomplete({
		source: peopleRoles,
		autoFocus: true,
		delay: 10,
		minLength: 0
	});
	
	$( "input.articlereferences" ).autocomplete({
		source: 'lookup.php?type=articlereference',
		autoFocus: true,
		minLength: 3,
		delay: 100
	})

	$(".addnext").click( function() {
		var td = $(this).parent();
		var count = td.data('count') + 1;
		var html = '';
		html += '<input type="text" placeholder="Person" name="contributors[' + count + '][person]" class="peopletags" size=30 maxlength=150>';
		html += '<input type="text" placeholder="Role" name="contributors[' + count + '][role]" class="peopleroles" size=30 maxlength=150><br>';
		td.append(html);
		td.data('count', count);
		$( td ).find('input.peopletags')
			.autocomplete({
				source: 'lookup.php?type=person',
				autoFocus: true,
				delay: 50,
				minLength: 3
			})
		;
		$( td ).find('input.peopleroles')
			.autocomplete({
				source: peopleRoles,
				autoFocus: true,
				delay: 10,
				minLength: 0
			})
		;
	});

	$(".addnextref").click( function() {
		var td = $(this).parent();
		var html = '';
		html += '<input type="text" placeholder="Reference to existing entry" name="references[]" class="articlereferences" size="25"><br>';
		td.append(html);
		$( td ).find('input.articlereferences')
			.autocomplete({
				source: 'lookup.php?type=articlereference',
				autoFocus: true,
				minLength: 3,
				delay: 100
			})
	});

});
</script>
</head>

<body>
<?php
include("links.inc.php");
printinfo();

if ($magazine_id && $issue_id) {
	$mainlink = "magazine.php?magazine_id=" . $magazine_id;
	$publiclink = "../magazines?issue=" . $issue_id;
	list($magazine_name, $issue_title, $issue_releasename) = getrow("SELECT m.name, i.title, i.releasetext FROM issue i INNER JOIN magazine m ON i.magazine_id = m.id WHERE i.id = $issue_id");
	$files = getone("SELECT COUNT(*) FROM files WHERE issue_id = $issue_id");

	$articles = getall("
		SELECT article.id, article.page, article.title, article.description, article.articletype, article.game_id, g.title AS gametitle
		FROM article
		LEFT JOIN game g ON article.game_id = g.id
		WHERE issue_id = $issue_id
		ORDER BY article.page, article.title != '', article.id
	");
	$articles[] = [];
	$dirfiles = count(glob(DOWNLOAD_PATH . getcategorydir('issue') . "/" . $issue_id . "/*"));
	print '<p style="font-weight: bold;">Edit articles for: <a href="' . $mainlink . '" accesskey="w" title="Hotkey: W">' . htmlspecialchars($magazine_name) . '</a>: ' . htmlspecialchars($issue_title . ', ' . $issue_releasename) . ' <sup><a href="' . $publiclink . '" accesskey="q">[public page]</a></sup> - <a href="files.php?category=issue&data_id=' . $issue_id . '" accesskey="f">' . $files . '/' . ($dirfiles == 1 ? '1 file' : $dirfiles . ' files'). '</a> - <a href="showlog.php?category=issue&data_id=' . $issue_id . '">Show log</a></p>';
	// print '<table><tr><th>Edit articles for: <a href="' . $mainlink . '">' . htmlspecialchars($magazine_name) . '</a>: ' . htmlspecialchars($issue_title) . '</a> <sup><a href="' . $publiclink . '" accesskey="q">[public page]</a></sup></th></tr>';
	print '<table>';

	foreach ($articles AS $article) {
		$article_id = $article['id'] ?? FALSE;
		$new = ! (bool) $article_id;
		$contributors = $references = [];
		$references_html = "";
		if ( ! $new) {
			// Non-optimal contributor and reference lookup
			$contributors = getall("SELECT c.person_id, c.person_extra, c.role, CONCAT(a.firstname, ' ', a.surname) AS name FROM contributor c LEFT JOIN person a ON c.person_id = a.id WHERE article_id = $article_id ORDER BY c.id");
			$references = getall("SELECT category, data_id FROM article_reference ar WHERE article_id = $article_id ORDER BY ar.id");
			foreach ($references AS $reference_id => $reference) {
				$entry = getentry($reference['category'], $reference['data_id']);
				$references[$reference_id]['label'] = $entry;
				$references_html = $entry;
			}
		}
		if (! $contributors) {
			$contributors[] = [];
		}
		if (! $references) {
			$references[] = [];
		}
		print '<tr><td>';
		print '<form action="magazine.php" method="post">'.
				'<input type="hidden" name="action" value="' . ($new ? 'addarticle' : 'changearticle') . '">'.
				'<input type="hidden" name="magazine_id" value="' . $magazine_id . '">'.
				'<input type="hidden" name="issue_id" value="' . $issue_id . '">'.
				'<input type="hidden" name="article_id" value="' . $article_id . '">';
		$person = (($article['person_id'] ?? FALSE) ? $article['person_id'] . " - " . $article['personname'] : $article['person_extra'] ?? '' );
		$game = (($article['game_id'] ?? FALSE) ? $article['game_id'] . " - " . $article['gametitle'] : '' );
		print "<table>";
		print '<tr valign="top" style="white-space: nowrap">' .
				'<td style="text-align:right; min-width: 2em;" ' . (($article['id'] ?? FALSE)  && $highlight_article_id == $article['id'] ? 'class="highlightarticle"' : '') . '>' . ($article['id'] ?? 'New') . '</td>'.
				'<td><input placeholder="Title" type="text" name="title" value="'.htmlspecialchars($article['title'] ?? '').'" size=30 maxlength=150 ' . ($new ? 'autofocus' : '') . '></td>' .
				'<td><input placeholder="Page" type="number" min="1" name="page" value="'.htmlspecialchars($article['page'] ?? '').'" style="width: 4em;"></td>';
		print '<td data-count="' . count($contributors) . '">';
		$pcount = 0;
		foreach ($contributors AS $contributor) {
			$pcount++;
			$person = (($contributor['person_id'] ?? FALSE) ? $contributor['person_id'] . ' - ' . $contributor['name'] : $contributor['person_extra'] ?? '' );
			print '<input type="text" placeholder="Person" name="contributors[' . $pcount . '][person]" class="peopletags" size=30 maxlength=150 value="' . htmlspecialchars($person ?? '') . '">';
			print '<input type="text" placeholder="Role" name="contributors[' . $pcount . '][role]" class="peopleroles" size=30 maxlength=150 value="' . htmlspecialchars($contributor['role'] ?? '') . '">';
			if ($pcount == 1) {
				print '<span class="addnext atoggle">➕</span>';
			}
			print '<br>';
		}

		print '</td>' .
				'<td><textarea placeholder="Description" name="description" cols="30" rows="1" onfocus="this.style.height=\'10em\'" onblur="this.style.height=\'1em\'" style="height: 1em;">'.htmlspecialchars($article['description'] ?? '').'</textarea></td>'.
				'<td><input placeholder="Article type" type="text" name="articletype" value="'.htmlspecialchars($article['articletype'] ?? '').'" size=15 maxlength=150></td>' .
				'<td>';
		$rcount = 0;
		foreach ($references AS $reference) {
			$label = '';
			if ($reference) {
				$label = getShortFromCategory($reference['category']) . $reference['data_id'] . ' - ' . $reference['label'];
			}
			$rcount++;
			print '<input type="text" placeholder="Reference to existing entry" name="references[]" class="articlereferences" size="25" value="' . htmlspecialchars($label) . '">';
			if ($rcount == 1) {
				print '<span class="addnextref atoggle">➕</span>';
			}
			print '<br>';
		}
		print '</td>' .
				'<td><input type="text" name="game_id" value="'.htmlspecialchars($game).'" class="gametags" size=20 maxlength=150 placeholder="Copy of existing game"></td>' .
				'<td><input type="submit" name="do" value="' . ($new ? 'Create' : 'Update') . '"> '.
				(! $new ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove article?\');">' : '') . '</td>'.
				"</tr>\n";
		print "</table>";
		print "</form>\n\n";
		print "</td></tr>";
		
	}
	print '</table></td></tr>';
	print '</tbody></table>';
	print '<p>Leave title and page blank for colophone</p>';
	print '<form action="magazine.php" method="post"><input type="hidden" name="action" value="duplicatearticle"><input type="hidden" name="magazine_id" value="' . $magazine_id . '"><input type="hidden" name="issue_id" value="' . $issue_id . '">';
	print '<p>Create duplicate of existing article/colophon into this issue. ID is shown left of articles. <input type="number" name="original_article_id" placeholder="ID of article" style="width: 7em;"> <input type="submit" value="Duplicate"></p>';
	print '</form>';

} elseif ($magazine_id) {
	$mainlink = "magazine.php";
	$publiclink = '../magazines.php?id=' . $magazine_id;
	$magazine_name = getone("SELECT name FROM magazine WHERE id = $magazine_id");
	
	$query = "
		SELECT i.id, i.title, i.releasedate, i.releasetext, i.internal, i.status, COUNT(a.id) AS entries, COUNT(f.id) AS files
		FROM issue i
		LEFT JOIN article a ON i.id = a.issue_id
		LEFT JOIN files f ON i.id = f.issue_id
		WHERE i.magazine_id = $magazine_id
		GROUP BY i.id, i.title, i.releasedate, i.releasetext
		ORDER BY i.releasedate, i.id
	";
	$issues = getall($query);
	$issues[] = [];
	print "<table align=\"center\" border=0><thead>".
	      '<tr><th colspan=5><a href="' . $mainlink . '">Magazines</a> - edit issues for: ' . htmlspecialchars($magazine_name) . ' <sup><a href="' . $publiclink . '" accesskey="q">[public page]</a></sup> - <a href="showlog.php?category=magazine&data_id=' . $magazine_id . '">Show log</a></th></tr>'. PHP_EOL .
		  "<tr>\n".
	      "<th>ID</th>".
	      "<th>Title</th>".
	      "<th>Release date (approx)</th>".
	      "<th>Release date text (e.g.: March 2012)</th>".
	      "<th>Internal note</th>".
	      "</tr>\n</thead><tbody>\n";

	foreach($issues AS $issue) {
		$statushtml = '<div class="issuestatus">';
		foreach($statuslist AS $statusid => $status) {
			$radioid = 'radio' . $status['label'] . '_' . (int) ($issue['id'] ?? 0);
			$statushtml .= '<input type="radio" name="status" value="' . $statusid . '" id="' . $radioid . '" ' . ($statusid == ($issue['status'] ?? FALSE) ? 'checked' : '') . '>';
			$statushtml .= '<label for="' . $radioid . '" title="' . htmlspecialchars($status['text']) . '" class="' . htmlspecialchars($status['label']) . '" >' . htmlspecialchars($status['short']) . '</label>' . PHP_EOL;
		}
		$statushtml .= '</div>';
		$new = ! isset($issue['id']);
		$dirfiles = count(glob(DOWNLOAD_PATH . getcategorydir('issue') . "/" . ($issue['id'] ?? '') . "/*"));
		print '<form action="magazine.php" method="post">'.
				'<input type="hidden" name="action" value="' . ($new ? 'addissue' : 'changeissue') . '">'.
				'<input type="hidden" name="magazine_id" value="' . $magazine_id . '">'.
				'<input type="hidden" name="issue_id" value="' . ($issue['id'] ?? '') . '">';
		print "<tr valign=\"top\">\n".
				'<td style="text-align:right;">' . ($issue['id'] ?? 'New') . '</td>'.
				'<td><input type="text" name="title" value="'.htmlspecialchars($issue['title'] ?? '') .'" size=40 maxlength=150>'.
				'<td><input type="date" name="releasedate" value="'.htmlspecialchars($issue['releasedate'] ?? '').'"></td>' .
				'<td><input type="text" name="releasetext" value="'.htmlspecialchars($issue['releasetext'] ?? '').'" size=40 maxlength=150></td>' .
				'<td rowspan="2"><textarea name="internal" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;">'.htmlspecialchars($issue['internal'] ?? '').'</textarea></td>'.
				'<td><input type="submit" name="do" value="' . ($new ? 'Create' : 'Update') . '"> '.
				(($issue['entries'] ?? 0) == 0 && ! $new ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove issue?\');">' : '') . '</td>'.
				"</tr>\n";
		if ( ! $new ) {
			print '<tr><td></td><td colspan="3">' . 
			$statushtml .
			'<a href="magazine.php?magazine_id=' . $magazine_id . '&amp;issue_id=' . $issue['id'] . '">' .
			($issue['entries'] == 1 ? '1 entry' : (int) $issue['entries'] . ' entries') .
			'</a> - <a href="files.php?category=issue&data_id=' . $issue['id'] . '">' . $issue['files'] . '/' . ($dirfiles == 1 ? '1 file' : $dirfiles . ' files'). '</a></td></tr>';
		}
		print "</form>\n\n";
	}
	print "</tbody></table>";
} else {
	$publiclink = '../magazines';
	$magazines = getall("SELECT m.id, m.name, m.description, m.internal, COUNT(i.id) AS issues FROM magazine m LEFT JOIN issue i ON m.id = i.magazine_id GROUP BY m.id, m.name, m.description ORDER BY m.name");
	print "<table align=\"center\" border=0><thead>".
	      '<tr><th colspan=4>Edit magazines <sup><a href="' . $publiclink . '" accesskey="q">[public page]</a></sup></th></tr>'.
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Magazine</th>".
	      "<th>Description</th>".
	      "<th>Internal note</th>".
	      "</tr></thead>\n<tbody>\n";

	foreach($magazines AS $magazine) {
		print '<form action="magazine.php" method="post">'.
				'<input type="hidden" name="action" value="changemagazine">'.
				'<input type="hidden" name="magazine_id" value="'.$magazine['id'].'">';
		print "<tr valign=\"top\">\n".
				'<td style="text-align:right;">'.$magazine['id'].'</td>'.
				'<td><input type="text" name="name" value="'.htmlspecialchars($magazine['name']).'" size=40 maxlength=150><br><a href="magazine.php?magazine_id=' . $magazine['id'] . '">' . ($magazine['issues'] == 1 ? "1 issue" : $magazine['issues'] . " issues") . '</a></td>'.
				'<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;">'.htmlspecialchars($magazine['description']).'</textarea></td>'.
				'<td><textarea name="internal" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;">'.htmlspecialchars($magazine['internal']).'</textarea></td>'.
				'<td><input type="submit" name="do" value="Update"> '.
				($magazine['issues'] == 0 ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove magazine?\');">' : '') . '</td>'.
				"</tr>\n";
		print "</form>\n\n";

	}
	print '<form action="magazine.php" method="post">'.
	      '<input type="hidden" name="action" value="addmagazine">';
	print "<tr valign=\"top\">\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="name" value="" size=40 maxlength=150></td>'.
	      '<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;"></textarea></td>'.
		  '<td><textarea name="internal" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;"></textarea></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      "</tr>\n";
	print "</form>\n\n";
	print "</tbody></table>" . PHP_EOL;

}

print '<p><a href="https://loot.alexandria.dk/files/magazines/">Magazine file storage</p>';

print "</body>\n</html>\n";

?>
