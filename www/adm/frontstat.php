<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$days = abs((int) ($_REQUEST['days'] ?? 7));

$result = getall("SELECT COUNT(*) AS c, user, user_id, MAX(`time`) AS latest, SUM(note REGEXP '^(Con|System|Person|Scenarie|Conset) oprettet$') AS oprettet FROM log WHERE `time` >= CURDATE() - INTERVAL $days DAY GROUP BY user, user_id ORDER BY c DESC, latest DESC, user");
print "<tr><th colspan=\"4\">Last $days days</th></tr>\n";
$rows = 0;
foreach($result AS $row) {
	$rows++;
	print "<tr>" .
	      "<td><a href=\"showlog.php?user_id=" . $row['user_id'] . "\">" . htmlspecialchars($row['user']) . "</a></td>" .
	      "<td style=\"text-align: right\">" . $row['oprettet'] . "</td>" .
	      "<td style=\"text-align: right\">" . $row['c'] . "</td>" .
	      "<td style=\"text-align: right\">" . pubdateprint($row['latest']) . "</td>" .
	      "</tr>\n";
}
if (!$rows) {
	print "<tr><td colspan=\"5\" style=\"text-align: center; font-style: italic;\">(None)</td></tr>\n";
}
?>
