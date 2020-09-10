<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$file_id = (int) $_REQUEST['file_id'];
$game_id = (int) $_REQUEST['game_id'];
$language = (string) $_REQUEST['language'];

if ($game_id && $file_id && $language) {
	$q = "UPDATE files SET " .
	"language = '" . dbesc($language) . "' " .
	"WHERE id = '$file_id'";
	$r = doquery($q);
	if ($r) {
		chlog($game_id,'sce',"Fil rettet");
	}
	header('Location: filesfix.php?game_id=' . $game_id . '#s_' . $game_id);
	exit;
}

$languages = ['da','en']
?>
<!DOCTYPE html>
<html>
<head>
<title>Country codes for files</title>
</head>
<body>
<?php
$files = getall("
	SELECT sce.id, sce.title, files.id AS filesid, files.filename, files.description, files.language
	FROM sce
	INNER JOIN files ON sce.id = files.data_id AND files.category = 'sce'
	WHERE files.downloadable = 1
");

$sce = [];
foreach($files AS $file) {
	$sce[$file['id']]['title'] = $file['title'];
	$sce[$file['id']]['files'][] = ['fileid' => $file['filesid'], 'filename' => $file['filename'], 'description' => $file['description'], 'language' => $file['language'] ];
}

print "<p>Scenarios:" . count($sce) . '</p>' . PHP_EOL;
print "<p>Files:" . count($files) . '</p>' . PHP_EOL;

print '<table><thead><tr><th>ID</th><th>Name</th><th colspan="10">Files</th></tr></thead><tbody>' . PHP_EOL;

foreach ($sce AS $sid => $s) {
	print '<tr id="s_' . $sid . '">';
	print '<td><a href="game.php?game=' . $sid . '">' . $sid . '</a></td>';
	print '<td><a href="../data?scenarie=' . $sid . '">' . htmlspecialchars($s['title']) . '</a></td>';
	foreach($s['files'] AS $file) {
		if ($file['language']) {
			print '<td><b>' . htmlspecialchars($file['description']) . ' [' . $alias['language'] . ']</b></td>';
		} else {
			print '<td>' . htmlspecialchars($alias['description']) . ' ';
			foreach ($languages AS $language) {
				print '[<a href="filesfix.php?game_id='. $sid . '&file_id=' . $file['fileid'] . '&language=' . $language . '">' . $language . ']</a> ';
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
	




