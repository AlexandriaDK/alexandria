<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$this_type = 'magazine';

$action = (string) $_REQUEST['action'];
$do = (string) $_REQUEST['do'];
$description = (string) $_REQUEST['description'];
$name = (string) $_REQUEST['name'];
$title = (string) $_REQUEST['title'];
$releasedate = (string) $_REQUEST['releasedate'];
$releasetext = (string) $_REQUEST['releasetext'];
$magazine_id = (int) $_REQUEST['magazine_id'];
$issue_id = (int) $_REQUEST['issue_id'];
$airel_id = (int) $_REQUEST['airel_id'];
$person = (string) $_REQUEST['person'];
$role = (string) $_REQUEST['role'];
$page = (int) $_REQUEST['page'];
$articletype = (string) $_REQUEST['articletype'];
$sce_id = (int) $_REQUEST['sce_id'];
$aut_id = (int) $person;
$aut_extra = ($aut_id ? '' : $person);

if ($issue_id && ! $magazine_id) {
	$magazine_id = getone("SELECT magazine_id FROM issue WHERE id = $issue_id");
}

$people = [];

$result = getall("SELECT id, firstname, surname FROM aut ORDER BY firstname, surname");
foreach($result AS $row) {
	$people[] = $row['id'] . " - " . $row['firstname'] . " " . $row['surname'];
}

$scenarios = [];
$result = getall("SELECT id, title FROM sce ORDER BY title");
foreach($result AS $row) {
	$scenarios[] = $row['id'] . " - " . $row['title'];
}

// Magazines
if ($action == "changemagazine" && $do != "Delete") {
	$q = "UPDATE magazine SET " .
	     "name = '" . dbesc($name) . "', " .
	     "description = '" . dbesc($description) . "' " .
	     "WHERE id = $magazine_id";
	$r = doquery($q);
	if ($r) {
		chlog($magazine_id,$this_type,"Magazine updated: $id, $name");
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
	     "(name, description) VALUES ".
	     "('" . dbesc($name) . "', '" . dbesc($description) . "')";
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
	     "releasetext = '" . dbesc($releasetext) . "' " .
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
	$q = "SELECT COUNT(*) FROM airel where issue_id = $issue_id";
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
	     "(title, releasedate, releasetext, magazine_id) VALUES ".
	     "('" . dbesc($title) . "', ". sqlifnull($releasedate) . ", '" . dbesc($releasetext) . "', $magazine_id)";
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
	$q = "UPDATE airel SET " .
	     "aut_id = " . sqlifnull($aut_id) . ", " .
	     "aut_extra = '" . dbesc($aut_extra) . "', " .
	     "role = '" . dbesc($role) . "', " .
	     "page = " . sqlifnull($page) . ", " .
	     "title = '" . dbesc($title) . "', " .
	     "description = '" . dbesc($description) . "', " .
	     "articletype = '" . dbesc($articletype) . "', " .
	     "sce_id = " . sqlifnull($sce_id) . " " .
	     "WHERE id = $airel_id";
	$r = doquery($q);
	if ($r) {
		chlog($issue_id,'issue',"Article updated: $title");
	}
	$_SESSION['admin']['info'] = "Article updated! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id ]);
}

if ($action == "changearticle" && $do == "Delete") {
	$q = "DELETE FROM airel WHERE id = $airel_id";
	$r = doquery($q);
	if ($r) {
		chlog($issue_id,'issue',"Article removed: $airel_id");
	}
	$_SESSION['admin']['info'] = "Article removed! " . dberror();
	rexit($this_type, ['magazine_id' => $magazine_id, 'issue_id' => $issue_id ]);
}

if ($action == "addarticle") {
	$q = "INSERT INTO airel " .
	     "(aut_id, issue_id, aut_extra, role, page, title, description, articletype, sce_id) VALUES ".
	     "(" . sqlifnull($aut_id) . ", $issue_id, '" . dbesc($aut_extra) . "', '" . dbesc($role) .  "', ".  sqlifnull($page) . ", '" . dbesc($title) . "', '" . dbesc($description) . "', '" . dbesc($articletype) . "', " . sqlifnull($sce_id) . ")";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($issue_id,'issue',"Article created: $id - $title");
	}
	$_SESSION['admin']['info'] = "Article created! " . dberror();
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
		var availablePeople = <?php print json_encode($people); ?>;
		$( ".peopletags" ).autocomplete({
			source: availablePeople,
			autoFocus: true,
			delay: 30,
			minLength: 3
		});
		var availableScenarios= <?php print json_encode($scenarios); ?>;
		$( ".scenariotags" ).autocomplete({
			source: availableScenarios,
			autoFocus: true,
			delay: 30,
			minLength: 3
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
	$issue_title = getone("SELECT title FROM issue WHERE id = $issue_id");

	$articles = getall("
		SELECT airel.id, airel.aut_id, airel.aut_extra, airel.role, airel.page, airel.title, airel.description, airel.articletype, airel.sce_id, CONCAT(aut.firstname, ' ', aut.surname) AS personname, sce.title AS scetitle
		FROM airel
		LEFT JOIN aut ON airel.aut_id = aut.id
		LEFT JOIN sce ON airel.sce_id = sce.id
		WHERE issue_id = $issue_id
		ORDER BY airel.page, airel.id
	");
	$articles[] = [];
	print "<table align=\"center\" border=0><thead>".
	      "<tr><th colspan=8>Edit articles for: <a href=\"$mainlink\" accesskey=\"q\">" . htmlspecialchars($issue_title) . "</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Title</th>".
	      "<th>Person</th>".
	      "<th>Role</th>".
	      "<th>Page</th>".
	      "<th>Description</th>".
	      "<th>Article type</th>".
	      "<th>Game</th>".
	      "</tr>\n</thead><tbody>";
	foreach ($articles AS $article) {
		$new = ! isset($article['id']);
		print '<form action="magazine.php" method="post">'.
				'<input type="hidden" name="action" value="' . ($new ? 'addarticle' : 'changearticle') . '">'.
				'<input type="hidden" name="magazine_id" value="' . $magazine_id . '">'.
				'<input type="hidden" name="issue_id" value="' . $issue_id . '">'.
				'<input type="hidden" name="airel_id" value="' . $article['id'] . '">';
		$person = ($article['aut_id'] ? $article['aut_id'] . " - " . $article['personname'] : $article['aut_extra'] );
		$game = ($article['sce_id'] ? $article['sce_id'] . " - " . $article['scetitle'] : '' );
		print "<tr valign=\"top\">\n".
				'<td style="text-align:right;">' . ($article['id'] ?? 'New') . '</td>'.
				'<td><input type="text" name="title" value="'.htmlspecialchars($article['title']).'" size=30 maxlength=150></td>' .
				'<td><input type="text" name="person" value="'.htmlspecialchars($person).'" class="peopletags" size=40 maxlength=150></td>' .
				'<td><input type="text" name="role" value="'.htmlspecialchars($article['role']).'" size=25 maxlength=150></td>' .
				'<td><input type="number" name="page" value="'.htmlspecialchars($article['page']).'" size=3></td>' .
				'<td><textarea name="description" cols="30" rows="1" onfocus="this.rows=10;" onblur="this.rows=1;" >'.htmlspecialchars($article['description']).'</textarea></td>'.
				'<td><input type="text" name="articletype" value="'.htmlspecialchars($article['articletype']).'" size=15 maxlength=150></td>' .
				'<td><input type="text" name="sce_id" value="'.htmlspecialchars($game).'" class="scenariotags" size=30 maxlength=150 placeholder="Existing game"></td>' .
				'<td><input type="submit" name="do" value="' . ($new ? 'Create' : 'Update') . '"> '.
				(! $new ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove article?\');">' : '') . '</td>'.
				"</tr>\n";
		print "</form>\n\n";
		
	}
	print '</tbody></table>';
	print '<p style="text-align: center;">Leave title and page blank for colophone</p>';

} elseif ($magazine_id) {
	$mainlink = "magazine.php";
	$magazine_name = getone("SELECT name FROM magazine WHERE id = $magazine_id");
	
	$query = "
		SELECT i.id, i.title, i.releasedate, i.releasetext, COUNT(a.id) AS entries, COUNT(f.id) AS files
		FROM issue i
		LEFT JOIN airel a ON i.id = a.issue_id
		LEFT JOIN files f ON i.id = f.data_id AND f.category = 'issue'
		WHERE i.magazine_id = $magazine_id
		GROUP BY i.id, i.title, i.releasedate, i.releasetext
	";
	$issues = getall($query);
	$issues[] = [];
	print "<table align=\"center\" border=0><thead>".
	      "<tr><th colspan=4>Edit issues for: <a href=\"$mainlink\" accesskey=\"q\">" . htmlspecialchars($magazine_name) . "</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Title</th>".
	      "<th>Release date (approx)</th>".
	      "<th>Issue name</th>".
	      "</tr>\n</thead><tbody>\n";

	foreach($issues AS $issue) {
		$new = ! isset($issue['id']);
		$dirfiles = count(glob(DOWNLOAD_PATH . getcategorydir('issue') . "/" . $issue['id'] . "/*"));
		print '<form action="magazine.php" method="post">'.
				'<input type="hidden" name="action" value="' . ($new ? 'addissue' : 'changeissue') . '">'.
				'<input type="hidden" name="magazine_id" value="' . $magazine_id . '">'.
				'<input type="hidden" name="issue_id" value="' . $issue['id'] . '">';
		print "<tr valign=\"top\">\n".
				'<td style="text-align:right;">' . ($issue['id'] ?? 'New') . '</td>'.
				'<td><input type="text" name="title" value="'.htmlspecialchars($issue['title']).'" size=40 maxlength=150>'.
				($new ? '' : '<br><a href="magazine.php?magazine_id=' . $magazine_id . '&amp;issue_id=' . $issue['id'] . '">' . ($issue['entries'] == 1 ? '1 entry' : (int) $issue['entries'] . ' entries'). '</a>' . ' - <a href="files.php?category=issue&data_id=' . $issue['id'] . '">' . $issue['files'] . '/' . ($dirfiles == 1 ? '1 file' : $dirfiles . ' files'). '</a>') . '</td>' .
				'<td><input type="date" name="releasedate" value="'.htmlspecialchars($issue['releasedate']).'"></td>' .
				'<td><input type="text" name="releasetext" value="'.htmlspecialchars($issue['releasetext']).'" size=40 maxlength=150></td>' .
				'<td><input type="submit" name="do" value="' . ($new ? 'Create' : 'Update') . '"> '.
				($issue['entries'] == 0 && ! $new ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove issue?\');">' : '') . '</td>'.
				"</tr>\n";
		print "</form>\n\n";
	}
	print "</tbody></table>";
} else {
	$magazines = getall("SELECT m.id, m.name, m.description, COUNT(i.id) AS issues FROM magazine m LEFT JOIN issue i ON m.id = i.magazine_id GROUP BY m.id, m.name, m.description ORDER BY m.name");
	print "<table align=\"center\" border=0><thead>".
	      "<tr><th colspan=3>Edit magazines</th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Magazine</th>".
	      "<th>Description</th>".
	      "</tr></thead>\n<tbody>\n";

	foreach($magazines AS $magazine) {
		print '<form action="magazine.php" method="post">'.
				'<input type="hidden" name="action" value="changemagazine">'.
				'<input type="hidden" name="magazine_id" value="'.$magazine['id'].'">';
		print "<tr valign=\"top\">\n".
				'<td style="text-align:right;">'.$magazine['id'].'</td>'.
				'<td><input type="text" name="name" value="'.htmlspecialchars($magazine['name']).'" size=40 maxlength=150><br><a href="magazine.php?magazine_id=' . $magazine['id'] . '">' . ($magazine['issues'] == 1 ? "1 issue" : $magazine['issues'] . " issues") . '</a></td>'.
				'<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;">'.htmlspecialchars($magazine['description']).'</textarea></td>'.
				'<td><input type="submit" name="do" value="Update"> '.
				($magazine['issues'] == 0 ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove magazine?\');">' : '') . '</td>'.
				"</tr>\n";
		print "</form>\n\n";

	}
	print '<form action="magazine.php" method="post">'.
	      '<input type="hidden" name="action" value="addmagazine">';
	print "<tr valign=\"top\">\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="name" value="" size=40 maxlength=150 autofocus></td>'.
	      '<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;"></textarea></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      "</tr>\n";
	print "</form>\n\n";
	print "</tbody></table>" . PHP_EOL;

}

print "</body>\n</html>\n";

?>
