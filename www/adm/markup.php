<?php
$admonly = true;
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$action = (string) ($_REQUEST['action'] ?? false);
$id = (int) ($_REQUEST['id'] ?? false);
$table = (string) ($_REQUEST['table'] ?? false);
$text = (string) ($_REQUEST['text'] ?? false);

if ($action) {
  validatetoken($token);
}

if ($action == 'fix') {
  updatestuff($table, $text, $id);
}

function updatestuff($table, $text, $id)
{
  $field = 'description';
  if ($table == 'trivia') {
    $field = 'fact';
  }
  $sql = "UPDATE $table SET $field = '" . dbesc($text) . "' WHERE id = $id";
  doquery($sql);
  header("Location: markup.php");
  exit;
}

function linkfix($matches)
{
  if ($id = getone("SELECT id FROM game WHERE title = '" . dbesc($matches[1]) . "'")) {
    $code = '[[[g' . $id . '|' . $matches[1] . ']]]';
    return $code;
  }
  if ($id = getone("SELECT id FROM person WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($matches[1]) . "'")) {
    $code = '[[[p' . $id . '|' . $matches[1] . ']]]';
    return $code;
  }
  return $matches[0];
}

htmladmstart("Markup fixes");

print "<h1>Markup fixes</h1>" . PHP_EOL;

$regexp_sql = '\\\[\\\[\\\[[^|]+\\\]\\\]\\\]';
$regexp_php = '_\\\[\\\[\\\[([^|]+)\\\]\\\]\\\]_';

$trivias = getall("select id, fact, COALESCE(game_id, convention_id, conset_id, gamesystem_id, tag_id) AS data_id, CASE WHEN !ISNULL(game_id) THEN 'game' WHEN !ISNULL(convention_id) THEN 'convention' WHEN !ISNULL(conset_id) THEN 'conset' WHEN !ISNULL(gamesystem_id) THEN 'gamesystem' WHEN !ISNULL(tag_id) THEN 'tag' END AS category from trivia where fact regexp '$regexp_sql'");
$tags = getall("select id, tag, description from tag where description regexp '$regexp_sql'");
$scenarios = getall("select gd.id, gd.game_id, g.title, gd.description from game_description gd inner join game g ON gd.game_id = g.id where gd.description regexp '$regexp_sql'");
$cons = getall("select id, description from convention where description regexp '$regexp_sql'");
$syss = getall("select id, description from gamesystem where description regexp '$regexp_sql'");

$total = count($trivias) + count($tags) + count($scenarios) + count($cons) + count($syss);
print dberror();

print "<table border=\"1\">" . PHP_EOL;
print "<tr><th colspan=\"2\">Trivia</th></tr>" . PHP_EOL;
foreach ($trivias as $trivia) {
  print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"trivia\"><input type=\"hidden\" name=\"id\" value=\"" . $trivia['id'] . "\">";
  print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
  print "<tr>";
  print "<td>" . htmlspecialchars($trivia['fact']) . "<br>";
  print "<a href=\"redir.php?cat=" . $trivia['category'] . "&data_id=" . $trivia['data_id'] . "\">[link]</a> ";
  print "<a href=\"trivia.php?category=" . $trivia['category'] . "&data_id=" . $trivia['data_id'] . "\">[trivia]</a>";

  print "</td>";
  $fixedfact = preg_replace_callback(
    '_\[\[\[([^]|]+)\]\]\]_',
    'linkfix',
    $trivia['fact']
  );
  print "<td>" . htmlspecialchars($fixedfact) . "<br><input type=\"hidden\" name=\"text\" value=\"" . htmlspecialchars($fixedfact) . "\"><input type=\"submit\"></td>";
  print "</tr>";
  print "</form>";
  print PHP_EOL;
}
print "</table>";

print "<table border=\"1\">" . PHP_EOL;
print "<tr><th colspan=\"2\">Tags</th></tr>" . PHP_EOL;
foreach ($tags as $tag) {
  print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"tag\"><input type=\"hidden\" name=\"id\" value=\"" . $tag['id'] . "\">";
  print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
  print "<tr>";
  print "<td>" . htmlspecialchars($tag['description']) . "<br>";
  print "<a href=\"tag.php?tag_id=" . $tag['id'] . "\">[tag]</a>";

  print "</td>";
  $fixedfact = preg_replace_callback(
    '_\[\[\[([^]|]+)\]\]\]_',
    'linkfix',
    $tag['description']
  );
  print "<td>" . htmlspecialchars($fixedfact) . "<br><input type=\"hidden\" name=\"text\" value=\"" . htmlspecialchars($fixedfact) . "\"><input type=\"submit\"></td>";
  print "</tr>";
  print "</form>";
  print PHP_EOL;
}
print "</table>";

print "<table border=\"1\">" . PHP_EOL;
print "<tr><th colspan=\"2\">Scenarios</th></tr>" . PHP_EOL;
foreach ($scenarios as $scenario) {
  print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"game_description\"><input type=\"hidden\" name=\"id\" value=\"" . $scenario['id'] . "\">";
  print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
  print "<tr>";
  print "<td>" . htmlspecialchars($scenario['description']) . "<br>";
  print "<a href=\"game.php?game=" . $scenario['game_id'] . "\">[scenario]</a>";

  print "</td>";
  $fixedfact = preg_replace_callback(
    '_\[\[\[([^]|]+)\]\]\]_',
    'linkfix',
    $scenario['description']
  );
  print "<td>" . htmlspecialchars($fixedfact) . "<br><input type=\"hidden\" name=\"text\" value=\"" . htmlspecialchars($fixedfact) . "\"><input type=\"submit\"></td>";
  print "</tr>";
  print "</form>";
  print PHP_EOL;
}
print "</table>";

print "<table border=\"1\">" . PHP_EOL;
print "<tr><th colspan=\"2\">Cons</th></tr>" . PHP_EOL;
foreach ($cons as $con) {
  print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"convention\"><input type=\"hidden\" name=\"id\" value=\"" . $con['id'] . "\">";
  print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
  print "<tr>";
  print "<td>" . nl2br(htmlspecialchars($con['description'])) . "<br>";
  print "<a href=\"convention.php?con=" . $con['id'] . "\">[convention]</a>";

  print "</td>";
  $fixedfact = preg_replace_callback(
    '_\[\[\[([^]|]+)\]\]\]_',
    'linkfix',
    $con['description']
  );
  print "<td>" . nl2br(htmlspecialchars($fixedfact)) . "<br><input type=\"hidden\" name=\"text\" value=\"" . htmlspecialchars($fixedfact) . "\"><input type=\"submit\"></td>";
  print "</tr>";
  print "</form>";
  print PHP_EOL;
}
print "</table>";

print "<table border=\"1\">" . PHP_EOL;
print "<tr><th colspan=\"2\">RPG Systems</th></tr>" . PHP_EOL;
foreach ($syss as $sys) {
  print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"gamesystem\"><input type=\"hidden\" name=\"id\" value=\"" . $sys['id'] . "\">";
  print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
  print "<tr>";
  print "<td>" . htmlspecialchars($sys['description']) . "<br>";
  print "<a href=\"gamesystem.php?gamesystem=" . $sys['id'] . "\">[RPG system]</a>";

  print "</td>";
  $fixedfact = preg_replace_callback(
    '_\[\[\[([^]|]+)\]\]\]_',
    'linkfix',
    $sys['description']
  );
  print "<td>" . htmlspecialchars($fixedfact) . "<br><input type=\"hidden\" name=\"text\" value=\"" . htmlspecialchars($fixedfact) . "\"><input type=\"submit\"></td>";
  print "</tr>";
  print "</form>";
  print PHP_EOL;
}
print "</table>";

if ($total == 0) {
  print "<p>No ambiguous links. Congratulations!</p>";
}

print "</body>\n</html>\n";
