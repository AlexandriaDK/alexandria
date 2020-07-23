<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'log';
$data_id = $_REQUEST['data_id'];
$category = $_REQUEST['category'];
$listlimit = (int) $_REQUEST['listlimit'];
$user_id = (int) $_REQUEST['user_id'];
if ($category == 'game') $category = 'sce';

if ($listlimit <= 0) {
	$listlimit = 100;
}

function admLink ($category, $data_id) {
	$link = "";
	if ($category == 'sce') return 'game.php?game=' . $data_id;
	if ($category == 'convent') return 'convent.php?con=' . $data_id;
	if ($category == 'conset') return 'conset.php?conset=' . $data_id;
	if ($category == 'aut') return 'person.php?person=' . $data_id;
	if ($category == 'sys') return 'system.php?system=' . $data_id;
	if ($category == 'tag') return 'tag.php?tag_id=' . $data_id;
	if ($category == 'review') return 'review.php?review_id=' . $data_id;

	return $link;
}

function getassoc($field, $table) {
	$data = [];
	$result = getall("SELECT id, $field AS field FROM $table");
	foreach($result AS $row) {	
		$data[$row['id']] = $row['field'];
	}
	return $data;

}

if ($data_id && $category) {
	$data_id = intval($data_id);
	switch($category) {
	case 'aut':
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
		break;
	case 'sce':
		$cat = 'sce';
		$q = "SELECT title FROM sce WHERE id = '$data_id'";
		$mainlink = "game.php?game=$data_id";
		break;
	case 'convent':
		$cat = 'convent';
		$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
		$mainlink = "convent.php?con=$data_id";
		break;
	case 'conset':
		$cat = 'conset';
		$q = "SELECT name FROM conset WHERE id = '$data_id'";
		$mainlink = "conset.php?conset=$data_id";
		break;
	case 'sys':
		$cat = 'sys';
		$q = "SELECT name FROM sys WHERE id = '$data_id'";
		$mainlink = "system.php?system=$data_id";
		break;
	case 'tag':
		$cat = 'tag';
		$q = "SELECT tag FROM tag WHERE id = '$data_id'";
		$mainlink = "tag.php?tag_id=$data_id";
		break;
	case 'review':
		$cat = 'review';
		$q = "SELECT title FROM reviews WHERE id = $data_id";
		$mainlink = "review.php?review_id=$data_id";
		break;
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, time, user, note FROM log WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id DESC";
	$result = getall($query);

} else {
	$data = [
		'aut' => getassoc("CONCAT(firstname,' ',surname)", "aut"),
		'sce' => getassoc("title", "sce"),
		'convent' => getassoc("CONCAT(name,' (',COALESCE(year,'?'),')')","convent"),
		'conset' => getassoc("name","conset"),
		'sys' => getassoc("name","sys"),
		'tag' => getassoc("tag","tag"),
		'review' => getassoc("title","review")
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

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

if ($result) {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Log for: <a href=\"$mainlink\" accesskey=\"q\">" . ( $title != "" ? htmlspecialchars($title) : '(unknown)' ) . "</a></th></tr>\n".
	      "<tr>\n".
	      "<th style=\"width: 180px\">Edited by</th>".
	      "<th style=\"width: 180px\">Time</th>".
	      "<th style=\"width: 160px\">Description</th>".
	      "</tr>\n";

        foreach($result AS $row) {
		print "<tr>\n".
		      "<td>".$row['user']."</td>\n".
		      "<td style=\"text-align: right;\">".pubdateprint($row['time'])."</td>\n".
		      "<td style=\"text-align: center;\">{$row['note']}</td>\n".
		      "</tr>\n";
	}
	print "</table>\n";
} else {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>$listlimit recent edits" . ($user_name ? " by " . htmlspecialchars($user_name) : "") . ":" . ($listlimit == 100 ? ' <a href="showlog.php?listlimit=1000' . ($user_id ? '&amp;user_id=' . $user_id : '') . '">[show 1,000]</a>' : '') . "</th></tr>\n".
	      "<tr>\n".
	      "<th>Entity</th>".
	      "<th style=\"width: 180px\">Edited by</th>".
	      "<th style=\"width: 180px\">Time</th>".
	      "<th style=\"width: 160px\">Description</th>".
	      "</tr>\n";

        foreach($listresult AS $row) {
		if ($data[$row['category']][$row['data_id']]) {
			$subject = $data[$row['category']][$row['data_id']];
			$link = admLink($row['category'], $row['data_id']);
		} else {
			$subject = $row['category'];
			if ( $row['data_id'] != NULL ) {
				$subject .= ": #" . $row['data_id'];
			}
			$link = "";
		}
		print "<tr>\n".
		      ($link ? "<td><a href=\"$link\">$subject</a></td>\n" : "<td>".$subject."</td>\n" ).
		      "<td>".$row['user']."</td>\n".
		      "<td style=\"text-align: right;\">".pubdateprint($row['time'])."</td>\n".
		      "<td style=\"text-align: right;\">{$row['note']}</td>\n".
		      "</tr>\n";
	}

	print "</table>\n";
	
}

print "<p>&nbsp;</p>\n<p style=\"text-align: center\">Logging blev først påbegyndt i marts 2002;<br />\nder kan derfor forefindes entries uden log-data.</p>\n";
print "<p style=\"text-align: center\">Ændringer i hvilke personer, der er tilknyttet scenarier, samt<br />\nhvilke conner, et scenarie har været spillet på, logges<br />\nkun som en scenarie-ændring.</p>\n";

?>

</body>
</html>
