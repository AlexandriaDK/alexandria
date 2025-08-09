<?php
$admonly = TRUE;
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$this_type = 'achievements';

$action = $_REQUEST['action'] ?? '';
$label = $_REQUEST['label'] ?? '';
$description = $_REQUEST['description'] ?? '';
$id = (int) ($_REQUEST['id'] ?? 0);
$do = $_REQUEST['do'] ?? '';
$order = $_REQUEST['order'] ?? '';
$user_id = (int) ($_REQUEST['user_id'] ?? 0);
$points = (int) ($_REQUEST['points'] ?? 0);

if ($action) {
  validatetoken($token);
}

// Ret achievement
if ($action == "update") {
  $q = "UPDATE achievements SET " .
    "label = '" . dbesc($label) . "', " .
    "description = '" . dbesc($description) . "', " .
    "points = $points " .
    "WHERE id = $id";
  $r = doquery($q);
  $_SESSION['admin']['info'] = "Achievement rettet! " . dberror();
  rexit($this_type);
}

if ($order == 'awarded') {
  $orderby = 'count DESC, available DESC, a.id';
} elseif ($order == 'latest') {
  $orderby = 'completed DESC, a.id';
} elseif ($order == 'id') {
  $orderby = 'a.id';
} else {
  $orderby = 'completed DESC, a.id';
}
if ($user_id) {
  $query = "SELECT a.id, a.label, a.description, a.available, COUNT(b.id) AS count, MAX(b.completed) AS completed, points FROM achievements a LEFT JOIN user_achievements b ON a.id = b.achievement_id AND b.user_id = $user_id GROUP BY a.id ORDER BY $orderby";
} else {
  $query = "SELECT a.id, a.label, a.description, a.available, COUNT(b.id) AS count, MAX(b.completed) AS completed, points FROM achievements a LEFT JOIN user_achievements b ON a.id = b.achievement_id GROUP BY a.id ORDER BY $orderby";
}
$result = getall($query);
$totalpoints = 0;

htmladmstart("Achievements");

print "<table align=\"center\" border=0>" .
  "<tr><th colspan=5>Achievements</th></tr>\n" .
  "<tr>\n" .
  "<th><a href=\"achievements.php?order=id\">ID</a></th>" .
  "<th>Label</th>" .
  "<th>Description</th>" .
  "<th>Points</th>" .
  "<th><a href=\"achievements.php?order=awarded\">Awarded</a></th>" .
  "<th>Available</th>" .
  "<th><a href=\"achievements.php?order=latest\">Most recent</a></th>" .
  "</tr>\n";

if ($result) {
  foreach ($result as $row) {
    $totalpoints += $row['points'];
    print '<form action="achievements.php" method="post">' .
      '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
      '<input type="hidden" name="action" value="update">' .
      '<input type="hidden" name="id" value="' . $row['id'] . '">';
    print "<tr valign='top'>\n" .
      '<td style="text-align:right;">' . $row['id'] . '</td>' .
      '<td><input type="text" name="label" value="' . htmlspecialchars($row['label']) . '" size=50 maxlength=100></td>' .
      '<td><input type="text" name="description" value="' . htmlspecialchars($row['description']) . '" size=50 maxlength=100></td>' .
      '<td><input type="number" name="points" value="' . $row['points'] . '" min="5" max="100" step="5"></td>' .
      '<td align="right"><a href="users.php?achievement_id=' . $row['id'] . '">' . htmlspecialchars($row['count']) . '</a></td>' .
      '<td align="center">' . ($row['available'] ? 'Yes' : '<b>No</b>') . '</td>' .
      '<td align="right" title="' . htmlspecialchars($row['name'] ?? '') . '">' . ($row['count'] ? pubdateprint($row['completed']) : '-') . '</td>' .
      '<td><input type="submit" name="do" value="Ret"></td>' .
      "</tr>\n";
    print "</form>\n\n";
  }
}

print "</table>\n";
print "<p>Total points: $totalpoints</p>\n";
print "</body>\n</html>\n";
