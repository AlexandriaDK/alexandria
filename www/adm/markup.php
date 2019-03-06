<?php
$admonly = TRUE;
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$action = (string) $_REQUEST['action'];
$id = (int) $_REQUEST['id'];
$table = (string) $_REQUEST['table'];
$text = (string) $_REQUEST['text'];

if ($action == 'fix') {
	updatestuff($table, $text, $id);
}

function updatestuff ($table, $text, $id) {
	$sql = "UPDATE $table SET description = '" . dbesc($text) . "' WHERE id = $id";
	doquery($sql);
	header("Location: markup.php");
	exit;
}

function linkfix ($matches) {
	if ($id = getone("SELECT id FROM sce WHERE title = '" . dbesc($matches[1]) . "'") ) {
		$code = '[[[s' . $id . '|' . $matches[1] . ']]]';
		return $code;
	}
	if ($id = getone("SELECT id FROM aut WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($matches[1]) . "'") ) {
		$code = '[[[p' . $id . '|' . $matches[1] . ']]]';
		return $code;
	}
	return $matches[0];
}
?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Markup-fixes</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body>
<?php
include("links.inc");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

print "<h1>Markup-fixes:</h1>" . PHP_EOL;

$regexp_sql = '\\\[\\\[\\\[[^|]+\\\]\\\]\\\]';
$regexp_php = '_\\\[\\\[\\\[([^|]+)\\\]\\\]\\\]_';
$sql = "select id, fact from trivia where fact regexp '$regexp_sql'";
$trivias = getall("select id, fact, data_id, category from trivia where fact regexp '$regexp_sql'");
$tags = getall("select id, tag, description from tag where description regexp '$regexp_sql'");
$scenarios = getall("select id, title, description from sce where description regexp '$regexp_sql'");
$cons = getall("select id, description from convent where description regexp '$regexp_sql'");

$total = count($trivia) + count($tags);
print dberror();

print "<table border=\"1\">" . PHP_EOL;
print "<th colspan=\"2\">Trivia</th>" . PHP_EOL;
foreach($trivias AS $trivia) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"trivia\"><input type=\"hidden\" name=\"id\" value=\"" . $trivia['id'] . "\">";
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
print "<th colspan=\"2\">Tags</th>" . PHP_EOL;
foreach($tags AS $tag) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"tag\"><input type=\"hidden\" name=\"id\" value=\"" . $tag['id'] . "\">";
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
print "<th colspan=\"2\">Scenarier</th>" . PHP_EOL;
foreach($scenarios AS $scenario) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"sce\"><input type=\"hidden\" name=\"id\" value=\"" . $scenario['id'] . "\">";
	print "<tr>";
	print "<td>" . htmlspecialchars($scenario['description']) . "<br>";
	print "<a href=\"scenarie.php?scenarie=" . $scenario['id'] . "\">[scenarie]</a>";

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
print "<th colspan=\"2\">Cons</th>" . PHP_EOL;
foreach($cons AS $con) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"convent\"><input type=\"hidden\" name=\"id\" value=\"" . $con['id'] . "\">";
	print "<tr>";
	print "<td>" . htmlspecialchars($con['description']) . "<br>";
	print "<a href=\"scenarie.php?scenarie=" . $con['id'] . "\">[scenarie]</a>";

	print "</td>";
	$fixedfact = preg_replace_callback(
		'_\[\[\[([^]|]+)\]\]\]_',
		'linkfix',
		$con['description']
	);
	print "<td>" . htmlspecialchars($fixedfact) . "<br><input type=\"hidden\" name=\"text\" value=\"" . htmlspecialchars($fixedfact) . "\"><input type=\"submit\"></td>";
	print "</tr>";
	print "</form>";
	print PHP_EOL;
}
print "</table>";

if ($total == 0) {
	print "<p>Ingen flertydige links. Tillykke!</p>";
}

print "</body>\n</html>\n";

?>
