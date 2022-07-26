<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'tags';

$action = (string) ($_REQUEST['action'] ?? '');
$do = (string) ($_REQUEST['do'] ?? '');
$tag = trim((string) ($_REQUEST['tag'] ?? ''));
$id = (int) ($_REQUEST['id'] ?? '');
$category = (string) ($_REQUEST['category'] ?? '');
$data_id = (int) ($_REQUEST['data_id'] ?? '');
if ($category == 'game') $category = 'sce';

// Update tag
if ($action == "changetag" && $do != "Remove") {
	$url = trim($url);
	$description = trim($description);
	$q = "UPDATE tags SET " .
		"tag = '" . dbesc($tag) . "', " .
		"added_by_user_id = '" . (int) $_SESSION['user_id'] . "' " .
		"WHERE id = $id";
	$r = doquery($q);
	if ($r) {
		chlog($data_id, $category, "Tag changed to $tag");
	}
	$_SESSION['admin']['info'] = "Tag updated! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Remove tag
if ($action == "changetag" && $do == "Remove") {
	$tag = getone("SELECT tag FROM tags WHERE id = $id");
	$q = "DELETE FROM tags WHERE id = $id";
	$r = doquery($q);
	if ($r) {
		chlog($data_id, $category, "Tag removed: $tag");
	}
	$_SESSION['admin']['info'] = "Tag removed! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Add tag
if ($action == "addtag" && $tag != "") {
	$q = "INSERT INTO tags " .
		"(game_id, tag, added_by_user_id) VALUES " .
		"($data_id, '" . dbesc($tag) . "', " . (int) $_SESSION['user_id'] . ")";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id, $category, "Tag added: $tag");
	}
	$_SESSION['admin']['info'] = "Tag added! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}


if ($data_id && $category) { // Does this make sense? Tags are only for games.
	$data_id = intval($data_id);
	switch ($category) {
		case 'aut':
			$cat = 'aut';
			$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM person WHERE id = '$data_id'";
			$mainlink = "person.php?person=$data_id";
			break;
		case 'sce':
			$cat = 'sce';
			$q = "SELECT title FROM game WHERE id = '$data_id'";
			$mainlink = "game.php?game=$data_id";
			break;
		case 'convent':
			$cat = 'convent';
			$q = "SELECT CONCAT(name, ' (', year, ')') FROM convention WHERE id = '$data_id'";
			$mainlink = "convention.php?con=$data_id";
			break;
		case 'conset':
			$cat = 'conset';
			$q = "SELECT name FROM conset WHERE id = '$data_id'";
			$mainlink = "conset.php?conset=$data_id";
			break;
		case 'sys':
		case 'gamesystem':
			$cat = 'sys';
			$q = "SELECT name FROM gamesystem WHERE id = '$data_id'";
			$mainlink = "gamesystem.php?system=$data_id";
			break;
		default:
			$cat = 'aut';
			$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM person WHERE id = '$data_id'";
			$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);

	$query = "SELECT id, tag FROM tags WHERE game_id = $data_id ORDER BY tag";
	$result = getall($query);
}

htmladmstart("Tags");

if ($data_id && $category) {
	print "<table align=\"center\" border=0>" .
		"<tr><th colspan=5>Edit tags for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n" .
		"<tr>\n" .
		"<th>ID</th>" .
		"<th>Tag</th>" .
		"</tr>\n";

	foreach ($result as $row) {
		print '<form action="tags.php" method="post">' .
			'<input type="hidden" name="action" value="changetag">' .
			'<input type="hidden" name="data_id" value="' . $data_id . '">' .
			'<input type="hidden" name="category" value="' . htmlspecialchars($category) . '">' .
			'<input type="hidden" name="id" value="' . $row['id'] . '">';
		print "<tr>\n" .
			'<td style="text-align:right;">' . $row['id'] . '</td>' .
			'<td><input type="text" name="tag" value="' . htmlspecialchars($row['tag']) . '" size=40 maxlength=100></td>' .
			'<td><input type="submit" name="do" value="Update"></td>' .
			'<td><input type="submit" name="do" value="Remove"></td>' .
			"</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="tags.php" method="post">' .
		'<input type="hidden" name="action" value="addtag">' .
		'<input type="hidden" name="data_id" value="' . $data_id . '">' .
		'<input type="hidden" name="category" value="' . htmlspecialchars($category) . '">';
	print "<tr>\n" .
		'<td style="text-align:right;">New</td>' .
		'<td><input type="text" name="tag" value="" size=40 maxlength=100></td>' .
		'<td colspan=2><input type="submit" name="do" value="Add"></td>' .
		"</tr>\n";
	print "</form>\n\n";

	print "</table>\n";
} else {
	print "Error: No data id provided.";
}
print "</body>\n</html>\n";
