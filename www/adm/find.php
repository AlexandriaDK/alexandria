<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";

$find = $_REQUEST['find'] ?? '';

// Lidt kvik-find-kode:

if (preg_match("/^([csgpfatiml#]|cs|sys)(\d+)$/i", $find, $regs)) {
  $pref = strtolower($regs[1]);
  $id = $regs[2];

  $url = "";
  switch ($pref) {
    case "s":
    case "g":
      $url = "game.php?game=" . $id;
      break;

    case "sys":
      $url = "gamesystem.php?gamesystem=" . $id;
      break;

    case "i":
      $url = "magazine.php?issue_id=" . $id;
      break;

    case "m":
      $url = "magazine.php?magazine_id=" . $id;
      break;

    case "c":
      $url = "convention.php?con=" . $id;
      break;

    case "cs":
      $url = "conset.php?conset=" . $id;
      break;

    case "p":
    case "f":
    case "a":
      $url = "person.php?person=" . $id;
      break;

    case "l":
      $url = "locations.php?id=" . $id;
      break;

    case "t":
    case "#":
      $url = "ticket.php?id=" . $id;
      break;
  }
  if ($url) {
    header("Location: " . $url);
    exit;
  }
}

$r1 = getall("
	(SELECT id, title, 0 AS alias FROM game WHERE title LIKE '%" . likeesc($find) . "%')
	UNION
	(SELECT game_id, label AS title, 1 AS alias FROM alias WHERE label LIKE '%" . likeesc($find) . "%' AND game_id IS NOT NULL)
	ORDER BY title
");
$r2 = getall("
	(SELECT id, CONCAT(firstname,' ',surname) AS name, 0 AS alias FROM person p WHERE CONCAT(firstname,' ',surname) LIKE '%" . likeesc($find) . "%')
	UNION
	(SELECT person_id, label AS name, 1 AS alias FROM alias WHERE label LIKE '%" . likeesc($find) . "%' AND person_id IS NOT NULL)
	ORDER BY name
");

if (count($r1) == 1 && count($r2) == 0) {
  $id = $r1[0][0];
  header("Location: game.php?game=$id");
  exit;
} elseif (count($r1) == 0 && count($r2) == 1) {
  $id = $r2[0][0];
  header("Location: person.php?person=$id");
  exit;
}

htmladmstart("Search");

print "<b>Found results:</b><br>";

print "Scenarios:<br>";

$antal = 0;
foreach ($r1 as list($id, $name, $alias)) {
  print "&nbsp;&nbsp;<a href=\"game.php?game=$id\" " . ($alias ? 'style="font-style: italic;"' : '') . ">$name</a><br>\n";
  $antal++;
}
if ($antal == 0) print "&nbsp;&nbsp;None<br>";
print "<br>\n\n";

print "People:<br>\n";

$antal = 0;

foreach ($r2 as list($id, $name, $alias)) {
  print "&nbsp;&nbsp;<a href=\"person.php?person=$id\" " . ($alias ? 'style="font-style: italic;"' : '') . ">$name</a><br>\n";
  $antal++;
}
if ($antal == 0) print "&nbsp;&nbsp;None<br>";

print "<br><a href=\"" . htmlspecialchars($_SERVER['HTTP_REFERER']) . "\">Back</a><br>";

?>
</body>

</html>