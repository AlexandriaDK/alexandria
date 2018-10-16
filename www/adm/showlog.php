<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'log';
$data_id = $_REQUEST['data_id'];
$category = $_REQUEST['category'];
$listlimit = (int) $_REQUEST['listlimit'];
$user_id = (int) $_REQUEST['user_id'];

if ($listlimit <= 0) {
	$listlimit = 100;
}

function admLink ($category, $data_id) {
	$link = "";
	if ($category == 'sce') return 'scenarie.php?scenarie=' . $data_id;
	if ($category == 'convent') return 'convent.php?con=' . $data_id;
	if ($category == 'conset') return 'conset.php?conset=' . $data_id;
	if ($category == 'aut') return 'person.php?person=' . $data_id;
	if ($category == 'sys') return 'system.php?system=' . $data_id;

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
		$mainlink = "scenarie.php?scenarie=$data_id";
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
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, time, user, ip, ip_forward, note FROM log WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id DESC";
	$result = getall($query);

} else {
	$data = [
		'aut' => getassoc("CONCAT(firstname,' ',surname)", "aut"),
		'sce' => getassoc("title", "sce"),
		'convent' => getassoc("CONCAT(name,' (',COALESCE(year,'?'),')')","convent"),
		'conset' => getassoc("name","conset"),
		'sys' => getassoc("name","sys")
	];
	if ($user_id) {
		$query = "SELECT id, data_id, category, time, user, user_id, ip, ip_forward, note FROM log WHERE user_id = $user_id ORDER BY id DESC LIMIT $listlimit";
	#	$user_result = mysql_query("SELECT name FROM users WHERE id = $user_id");
#		list($user_name) = mysql_fetch_row($user_result);
		$user_name = getone("SELECT name FROM users WHERE id = $user_id");
	} else {
		$query = "SELECT id, data_id, category, time, user, user_id, ip, ip_forward, note FROM log ORDER BY id DESC LIMIT $listlimit";
	}
	$listresult = getall($query);

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>Administration - log</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

if ($result) {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Log over: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th style=\"width: 180px\">Ændret af</th>".
	      "<th style=\"width: 180px\">Tidspunkt</th>".
	      "<th style=\"width: 160px\">Beskrivelse</th>".
	      "<th style=\"width: 120px\">IP</th>".
	      "</tr>\n";

        foreach($result AS $row) {
		$ip = $row['ip'];
		print "<tr>\n".
		      "<td>".$row['user']."</td>\n".
		      "<td style=\"text-align: right;\">".pubdateprint($row['time'])."</td>\n".
#		      "<td style=\"text-align: right;\">".date("j/n Y, H:i",strtotime($row['time']))."</td>\n".
#		      "<td>{$row['time']}</td>\n".
		      "<td style=\"text-align: center;\">{$row['note']}</td>\n".
		      "<td style=\"text-align: center;\">$ip</td>\n".
		      "</tr>\n";
	}
	print "</table>\n";
} else {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Seneste $listlimit ændringer" . ($user_name ? " af " . htmlspecialchars($user_name) : "") . ":" . ($listlimit == 100 ? ' <a href="showlog.php?listlimit=1000' . ($user_id ? '&amp;user_id=' . $user_id : '') . '">[vis 1000]</a>' : '') . "</th></tr>\n".
	      "<tr>\n".
	      "<th>Emne</th>".
	      "<th style=\"width: 180px\">Ændret af</th>".
	      "<th style=\"width: 180px\">Tidspunkt</th>".
	      "<th style=\"width: 160px\">Beskrivelse</th>".
	      "<th style=\"width: 120px\">IP</th>".
	      "</tr>\n";

        foreach($listresult AS $row) {
		$ip = $row['ip'];
		if ($data[$row['category']][$row['data_id']]) {
			$subject = $data[$row['category']][$row['data_id']];
			$link = admLink($row['category'], $row['data_id']);
		} else {
			$subject = $row['category'] . ": #" . $row['data_id'];
			$link = "";
		}
		print "<tr>\n".
		      ($link ? "<td><a href=\"$link\">$subject</a></td>\n" : "<td>".$subject."</td>\n" ).
		      "<td>".$row['user']."</td>\n".
		      "<td style=\"text-align: right;\">".pubdateprint($row['time'])."</td>\n".
		      "<td style=\"text-align: right;\">{$row['note']}</td>\n".
		      "<td style=\"text-align: right;\">$ip</td>\n".
		      "</tr>\n";
	}

	print "</table>\n";
	
}


print "<p>&nbsp;</p>\n<p style=\"text-align: center\">Logging blev først påbegyndt i marts 2002;<br />\nder kan derfor forefindes entries uden log-data.</p>\n";
print "<p style=\"text-align: center\">Ændringer i hvilke personer, der er tilknyttet scenarier, samt<br />\nhvilke conner, et scenarie har været spillet på, logges<br />\nkun som en scenarie-ændring.</p>\n";

?>

</body>
</html>
