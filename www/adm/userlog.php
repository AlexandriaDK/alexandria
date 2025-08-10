<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'links';

$category = $_REQUEST['category'];
$data_id = $_REQUEST['data_id'];

if (!$data_id || !in_array($category, ['game', 'convention'])) {
  htmladmstart("User log");
  print "<p>Bad data_id or category</p>";
  print "</body>\n</html>\n";
  exit;
}

$data_id = intval($data_id);
$data_field = getFieldFromCategory($category);
switch ($category) {
  case 'game':
    $q = "SELECT title FROM game WHERE id = '$data_id'";
    $mainlink = "game.php?game=$data_id";
    break;
  case 'convention':
    $q = "SELECT CONCAT(name, ' (', year, ')') FROM convention WHERE id = '$data_id'";
    $mainlink = "convention.php?con=$data_id";
    break;
  default:
}
$title = getone($q);

$query = "SELECT a.user_id, type, added, b.name FROM userlog a INNER JOIN users b ON a.user_id = b.id WHERE `$data_field`= '$data_id' ORDER BY b.name";
$result = getall($query);
$dataset = [];
foreach ($result as $row) {
  $dataset[$row['user_id']]['name'] = $row['name'];
  $dataset[$row['user_id']]['data'][$row['type']] = true;
}

htmladmstart("User log");

print "<table align=\"center\" border=0>" .
  "<tr><th colspan=5>User log: <a href=\"$mainlink\" accesskey=\"q\">$title</a> (" . count($dataset) . " " . (count($dataset) == 1 ? "person" : "personer") . ")</th></tr>\n" .
  "<tr class=\"headline\">\n" .
  "<th>User</th>";

if ($category  == "game") {
  print "<th>Read</th><th>GMed</th><th>Played</th>";
} else {
  print "<th>Visited</th>";
}

foreach ($dataset as $user) {
  print "<tr>";
  print "<td>" . $user['name'] . "</td>";
  if ($category == "game") {
    print "<td class=\"mark\">" . ($user['data']['read'] ?? false ? "✔" : "") . "</td>";
    print "<td class=\"mark\">" . ($user['data']['gmed'] ?? false ? "✔" : "") . "</td>";
    print "<td class=\"mark\">" . ($user['data']['played'] ?? false ? "✔" : "") . "</td>";
  } else {
    print "<td class=\"mark\">" . ($user['data']['visited'] ?? false ? "✔" : "") . "</td>";
  }
  print "</tr>" . PHP_EOL;
}

print "</table>\n";
print "</body>\n</html>\n";
