<?php
$admonly = TRUE;
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$action = (string) $_REQUEST['action'];
$id = (int) $_REQUEST['id'];
$table = (string) $_REQUEST['table'];
$text = (string) $_REQUEST['text'];

if ( $action ) {
	validatetoken( $token );
}

if ($action == 'fix') {
	updatestuff($table, $text, $id);
}

function updatestuff ($table, $text, $id) {
	$field = 'description';
	if ($table == 'trivia') {
		$field = 'fact';
	}
	$sql = "UPDATE $table SET $field = '" . dbesc($text) . "' WHERE id = $id";
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

htmladmstart("Markup fixes");

print "<h1>Markup fixes</h1>" . PHP_EOL;

$regexp_sql = '\\\[\\\[\\\[[^|]+\\\]\\\]\\\]';
$regexp_php = '_\\\[\\\[\\\[([^|]+)\\\]\\\]\\\]_';
$sql = "select id, fact from trivia where fact regexp '$regexp_sql'";
$trivias = getall("select id, fact, data_id, category from trivia where fact regexp '$regexp_sql'");
$tags = getall("select id, tag, description from tag where description regexp '$regexp_sql'");
$scenarios = getall("select id, title, description from sce where description regexp '$regexp_sql'");
$cons = getall("select id, description from convent where description regexp '$regexp_sql'");
$syss = getall("select id, description from sys where description regexp '$regexp_sql'");

$total = count($trivias) + count($tags) + count($scenarios) + count($cons) + count($syss);
print dberror();

print "<table border=\"1\">" . PHP_EOL;
print "<tr><th colspan=\"2\">Trivia</th></tr>" . PHP_EOL;
foreach($trivias AS $trivia) {
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
foreach($tags AS $tag) {
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
foreach($scenarios AS $scenario) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"sce\"><input type=\"hidden\" name=\"id\" value=\"" . $scenario['id'] . "\">";
	print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
	print "<tr>";
	print "<td>" . htmlspecialchars($scenario['description']) . "<br>";
	print "<a href=\"game.php?game=" . $scenario['id'] . "\">[scenario]</a>";

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
foreach($cons AS $con) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"convent\"><input type=\"hidden\" name=\"id\" value=\"" . $con['id'] . "\">";
	print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
	print "<tr>";
	print "<td>" . nl2br(htmlspecialchars($con['description'])) . "<br>";
	print "<a href=\"convent.php?con=" . $con['id'] . "\">[convention]</a>";

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
foreach($syss AS $sys) {
	print "<form action=\"markup.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"fix\"><input type=\"hidden\" name=\"table\" value=\"sys\"><input type=\"hidden\" name=\"id\" value=\"" . $sys['id'] . "\">";
	print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
	print "<tr>";
	print "<td>" . htmlspecialchars($sys['description']) . "<br>";
	print "<a href=\"system.php?system=" . $sys['id'] . "\">[RPG system]</a>";

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

?>
