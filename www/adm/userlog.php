<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'links';

$category = $_REQUEST['category'];
$data_id = $_REQUEST['data_id'];

if ($data_id && $category) {
	$data_id = intval($data_id);
	switch($category) {
	case 'sce':
		$cat = 'sce';
		$q = "SELECT title FROM sce WHERE id = '$data_id'";
		$mainlink = "scenarie.php?scenarie=$data_id";
		break;
	case 'convent':
		$cat = 'convent';
		$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
		$mainlink = "convent.php?con=$data_id";
		break;
	default:
	}
	$title = getone($q);
	
	$query = "SELECT a.user_id, type, added, b.name FROM userlog a INNER JOIN users b ON a.user_id = b.id WHERE data_id = '$data_id' AND category = '$cat' ORDER BY b.name";
	$result = getall($query);
	$dataset = [];
	foreach($result AS $row) {
		$dataset[$row['user_id']]['name'] = $row['name'];
		$dataset[$row['user_id']]['data'][$row['type']] = TRUE;
	}
}

htmladmstart("User log");

print "<table align=\"center\" border=0>".
      "<tr><th colspan=5>User log: <a href=\"$mainlink\" accesskey=\"q\">$title</a> (" . count($dataset) . " " . (count($dataset) == 1 ? "person" : "personer") . ")</th></tr>\n".
      "<tr class=\"headline\">\n".
      "<th>User</th>";

if ($category  == "sce") {
	print "<th>Read</th><th>GMed</th><th>Played</th>";
} else {
	print "<th>Visited</th>";
}

foreach($dataset AS $user) {
	print "<tr>";
	print "<td>" . $user['name'] . "</td>";
	if ($category == "sce") {
		print "<td class=\"mark\">" . ($user['data']['read'] ? "✔" : "") . "</td>";
		print "<td class=\"mark\">" . ($user['data']['gmed'] ? "✔" : "") . "</td>";
		print "<td class=\"mark\">" . ($user['data']['played'] ? "✔" : "") . "</td>";
	} else {
		print "<td class=\"mark\">" . ($user['data']['visited'] ? "✔" : "") . "</td>";
	}
	print "</tr>" . PHP_EOL;
}

print "</table>\n";
print "</body>\n</html>\n";

?>
