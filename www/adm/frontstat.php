<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";

$days = abs((int) ($_REQUEST['days'] ?? 7));

$result = getall("
	SELECT COUNT(*) AS c, users.name, log.user_id, MAX(`time`) AS latest, SUM(note REGEXP '^(Con|System|Person|Game|Con series) created$') AS created
	FROM log
	LEFT JOIN users ON log.user_id = users.id
	WHERE `time` >= CURDATE() - INTERVAL $days DAY
	GROUP BY log.user_id, users.name
	ORDER BY c DESC, latest DESC, users.name, log.user_id
");
print "<tr><th colspan=\"4\">Last $days days</th></tr>\n";
$rows = 0;
foreach ($result as $row) {
  $rows++;
  print "<tr>" .
    "<td><a href=\"showlog.php?user_id=" . $row['user_id'] . "\">" . htmlspecialchars($row['name']) . "</a></td>" .
    "<td style=\"text-align: right\">" . $row['created'] . "</td>" .
    "<td style=\"text-align: right\">" . $row['c'] . "</td>" .
    "<td style=\"text-align: right\">" . pubdateprint($row['latest']) . "</td>" .
    "</tr>\n";
}
if (!$rows) {
  print "<tr><td colspan=\"5\" style=\"text-align: center; font-style: italic;\">(None)</td></tr>\n";
}
