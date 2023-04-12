<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'log';
$data_id = (int) ($_REQUEST['data_id'] ?? 0);
$category = (string) ($_REQUEST['category'] ?? '');
$listlimit = (int) ($_REQUEST['listlimit'] ?? 0);
$user_id = (int) ($_REQUEST['user_id'] ?? 0);

if ($listlimit <= 0) {
	$listlimit = 100;
}

function admLink($category, $data_id)
{
	$link = "";
	if ($category == 'game') return 'game.php?game=' . $data_id;
	if ($category == 'convention') return 'convention.php?con=' . $data_id;
	if ($category == 'conset') return 'conset.php?conset=' . $data_id;
	if ($category == 'person') return 'person.php?person=' . $data_id;
	if ($category == 'gamesystem') return 'gamesystem.php?gamesystem=' . $data_id;
	if ($category == 'tag') return 'tag.php?tag_id=' . $data_id;
	if ($category == 'review') return 'review.php?review_id=' . $data_id;
	if ($category == 'issue') return 'magazine.php?issue_id=' . $data_id;
	if ($category == 'magazine') return 'magazine.php?magazine_id=' . $data_id;
	if ($category == 'locations') return 'locations.php?id=' . $data_id;

	return $link;
}

function getassoc($field, $table)
{
	$data = [];
	$result = getall("SELECT id, $field AS field FROM $table");
	foreach ($result as $row) {
		$data[$row['id']] = $row['field'];
	}
	return $data;
}

if ($data_id && $category) {
	$data_id = intval($data_id);
	switch ($category) {
		case 'person':
			$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM person WHERE id = '$data_id'";
			$mainlink = "person.php?person=$data_id";
			break;
		case 'game':
			$q = "SELECT title FROM game WHERE id = '$data_id'";
			$mainlink = "game.php?game=$data_id";
			break;
		case 'convention':
			$q = "SELECT CONCAT(name, ' (', year, ')') FROM convention WHERE id = '$data_id'";
			$mainlink = "convention.php?con=$data_id";
			break;
		case 'conset':
			$q = "SELECT name FROM conset WHERE id = '$data_id'";
			$mainlink = "conset.php?conset=$data_id";
			break;
		case 'gamesystem':
			$q = "SELECT name FROM gamesystem WHERE id = '$data_id'";
			$mainlink = "gamesystem.php?gamesystem=$data_id";
			break;
		case 'tag':
			$q = "SELECT tag FROM tag WHERE id = '$data_id'";
			$mainlink = "tag.php?tag_id=$data_id";
			break;
		case 'review':
			$q = "SELECT title FROM reviews WHERE id = $data_id";
			$mainlink = "review.php?review_id=$data_id";
			break;
		case 'issue':
			$q = "SELECT title FROM issue WHERE id = $data_id";
			$mainlink = "magazine.php?issue_id=$data_id";
			break;
		case 'magazine':
			$q = "SELECT name FROM magazine WHERE id = $data_id";
			$mainlink = "magazine.php?magazine_id=$data_id";
			break;
		case 'locations':
			$q = "SELECT name FROM locations WHERE id = $data_id";
			$mainlink = "location.php?id=$data_id";
			break;
		default:
			$category = 'person';
			$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM person WHERE id = '$data_id'";
			$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);

	$query = "SELECT id, time, user, note FROM log WHERE data_id = '$data_id' AND category = '$category' ORDER BY id DESC";
	$result = getall($query);
} else {
	$data = [
		'person' => getassoc("CONCAT(firstname,' ',surname)", "person"),
		'game' => getassoc("title", "game"),
		'convention' => getassoc("CONCAT(name,' (',COALESCE(year,'?'),')')", "convention"),
		'conset' => getassoc("name", "conset"),
		'gamesystem' => getassoc("name", "gamesystem"),
		'tag' => getassoc("tag", "tag"),
		'review' => getassoc("title", "reviews"),
		'issue' => getassoc("title", "issue"),
		'magazine' => getassoc("name", "magazine"),
		'locations' => getassoc("name", "locations"),

	];
	if ($user_id) {
		$query = "SELECT id, data_id, category, time, user, user_id, note FROM log WHERE user_id = $user_id ORDER BY id DESC LIMIT $listlimit";
		$user_name = getone("SELECT name FROM users WHERE id = $user_id");
	} else {
		$query = "SELECT id, data_id, category, time, user, user_id, note FROM log ORDER BY id DESC LIMIT $listlimit";
	}
	$listresult = getall($query);
}
htmladmstart("Log");

if (isset($result)) {
	print "<table align=\"center\" border=0>" .
		"<tr><th colspan=5>Log for: <a href=\"$mainlink\" accesskey=\"q\">" . ($title != "" ? htmlspecialchars($title) : '(unknown)') . "</a></th></tr>\n" .
		"<tr>\n" .
		"<th>Edited by</th>" .
		"<th>Time</th>" .
		"<th>Description</th>" .
		"</tr>\n";

	foreach ($result as $row) {
		print "<tr>\n" .
			"<td>" . $row['user'] . "</td>\n" .
			"<td style=\"text-align: right;\">" . pubdateprint($row['time']) . "</td>\n" .
			"<td style=\"text-align: center;\">{$row['note']}</td>\n" .
			"</tr>\n";
	}
	print "</table>\n";
} else {
	print "<table align=\"center\" border=0>" .
		"<tr><th colspan=5>$listlimit most recent edits" . ($user_name ?? FALSE ? " by " . htmlspecialchars($user_name) : "") . ":" . ($listlimit == 100 ? ' <a href="showlog.php?listlimit=1000' . ($user_id ? '&amp;user_id=' . $user_id : '') . '">[show 1,000]</a>' : '') . "</th></tr>\n" .
		"<tr>\n" .
		"<th>Entity</th>" .
		"<th>Edited by</th>" .
		"<th>Time</th>" .
		"<th>Description</th>" .
		"</tr>\n";
	foreach ($listresult as $row) {
		if (isset($data[$row['category']][$row['data_id']])) {
			$subject = $data[$row['category']][$row['data_id']];
			$link = admLink($row['category'], $row['data_id']);
		} else {
			$subject = $row['category'];
			if ($row['data_id'] != NULL) {
				$subject .= ": #" . $row['data_id'];
			}
			$link = "";
		}
		print "<tr>\n" .
			($link ? "<td><a href=\"$link\">$subject</a></td>\n" : "<td>" . $subject . "</td>\n") .
			"<td>" . $row['user'] . "</td>\n" .
			"<td style=\"text-align: right;\">" . pubdateprint($row['time']) . "</td>\n" .
			"<td style=\"width: 600px;\">" . htmlspecialchars($row['note']) . "</td>\n" .
			"</tr>\n";
	}

	print "</table>\n";
}

print "<p>&nbsp;</p>\n<p style=\"text-align: center\">Logging was enabled in March 2002.</p>\n";
?>

</body>

</html>