<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$game_id = (int) $_REQUEST['game_id'];
$alias_id = (int) $_REQUEST['alias_id'];
$language = (string) $_REQUEST['language'];

if ($game_id && $alias_id && $language) {
  $q = "UPDATE alias SET " .
    "language = '" . dbesc($language) . "' " .
    "WHERE id = '$alias_id'";
  $r = doquery($q);
  if ($r) {
    chlog($game_id, 'game', "Alias updated");
  }
  header('Location: aliasfix.php?game_id=' . $game_id . '#s_' . $game_id);
  exit;
}

$languages = ['da', 'en']
?>
<!DOCTYPE html>
<html>

<head>
  <title>Country codes for aliases</title>
</head>

<body>
  <?php
  $aliases = getall("
	SELECT g.id, g.title, alias.id AS aliasid, alias.label, alias.language
	FROM game g
	INNER JOIN alias ON g.id = alias.game_id
	WHERE alias.visible = 1
");

  $sce = [];
  foreach ($aliases as $alias) {
    $sce[$alias['id']]['title'] = $alias['title'];
    $sce[$alias['id']]['aliases'][] = ['aliasid' => $alias['aliasid'], 'alias' => $alias['label'], 'language' => $alias['language']];
  }

  print "<p>Scenarios:" . count($sce) . '</p>' . PHP_EOL;
  print "<p>Aliases:" . count($aliases) . '</p>' . PHP_EOL;

  print '<table><thead><tr><th>ID</th><th>Name</th><th colspan="10">Aliases</th></tr></thead><tbody>' . PHP_EOL;

  foreach ($sce as $sid => $s) {
    print '<tr id="s_' . $sid . '">';
    print '<td><a href="game.php?game=' . $sid . '">' . $sid . '</a></td>';
    print '<td><a href="../data?scenarie=' . $sid . '">' . htmlspecialchars($s['title']) . '</a></td>';
    foreach ($s['aliases'] as $alias) {
      if ($alias['language']) {
        print '<td><b>' . htmlspecialchars($alias['alias']) . ' [' . $alias['language'] . ']</b></td>';
      } else {
        print '<td>' . htmlspecialchars($alias['alias']) . ' ';
        foreach ($languages as $language) {
          print '[<a href="aliasfix.php?game_id=' . $sid . '&alias_id=' . $alias['aliasid'] . '&language=' . $language . '">' . $language . ']</a> ';
        }
        print '</td>';
      }
    }
    print '</tr>' . PHP_EOL;
  }

  print '</tbody></table>';

  ?>

</body>

</html>