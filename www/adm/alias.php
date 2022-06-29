<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'alias';

$action = (string) $_REQUEST['action'];
$do = (string) $_REQUEST['do'];
$label = trim((string) $_REQUEST['label']);
$language = trim((string) $_REQUEST['language']);
$visible = (string) $_REQUEST['visible'];
$visible = ($visible == "on" ? 1 : 0);
$id = (int) $_REQUEST['id'];
$data_id = (int) $_REQUEST['data_id'];
$category = (string) $_REQUEST['category'];
if ($category == 'game') $category = 'sce';

// Edit alias
if ($action == "changealias" && $do != "Delete") {
	$q = "UPDATE alias SET " .
	     "label = '" . dbesc($label) . "', " .
	     "language = '" . dbesc($language) . "', " .
	     "visible = '$visible' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Alias updated");
	}
	$_SESSION['admin']['info'] = "Alias updated! " . dberror();
	rexit( $this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Slet alias
if ($action == "changealias" && $do == "Delete") {
	$q = "DELETE FROM alias WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Alias deleted");
	}
	$_SESSION['admin']['info'] = "Alias deleted! " . dberror();
	rexit( $this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Tilføj alias
if ($action == "addalias") {
	$q = "INSERT INTO alias " .
	     "(data_id, category, label, language, visible) VALUES ".
	     "('$data_id', '$category', '" . dbesc($label) ."', '" . dbesc( $language ) . "', '$visible')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id,$category,"Alias created");
	}
	$_SESSION['admin']['info'] = "Alias created! " . dberror();
	rexit( $this_type, ['category' => $category, 'data_id' => $data_id] );
}

if ($data_id && $category) {
	$data_id = intval($data_id);
	switch($category) {
	case 'aut':
	case 'person':
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM person WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
		break;
	case 'sce':
	case 'game':
		$cat = 'sce';
		$q = "SELECT title FROM game WHERE id = '$data_id'";
		$mainlink = "game.php?game=$data_id";
		break;
	case 'convent':
	case 'convention':
		$cat = 'convent';
		$q = "SELECT CONCAT(name, ' (', year, ')') FROM convention WHERE id = '$data_id'";
		$mainlink = "convent.php?con=$data_id";
		break;
	case 'conset':
		$cat = 'conset';
		$q = "SELECT name FROM conset WHERE id = '$data_id'";
		$mainlink = "conset.php?conset=$data_id";
		break;
	case 'sys':
	case 'gamesystem':
		$cat = 'sys';
		$q = "SELECT name FROM gamesystem WHERE id = '$data_id'";
		$mainlink = "system.php?system=$data_id";
		break;
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM person WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, label, language, visible FROM alias WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id";
	$result = getall($query);
}

htmladmstart("Alias");

if ($data_id && $category) {

	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Edit aliases for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Alias</th>".
	      "<th>Language code</th>".
	      "<th>Visible</th>".
	      "</tr>\n";

	foreach($result AS $row) {
		$selected = ($row['visible'] == 1 ? 'checked="checked"' : '');
		print '<form action="alias.php" method="post">'.
		      '<input type="hidden" name="action" value="changealias">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="text" name="label" value="'.htmlspecialchars($row['label']).'" size="40" maxlength="150"></td>'.
		      '<td><input type="text" name="language" value="'.htmlspecialchars($row['language']).'" size="2" maxlength="20" placeholder="da"></td>'.
		      '<td><input type="checkbox" name="visible" '.$selected.'></td>'.
		      '<td><input type="submit" name="do" value="Update"></td>'.
		      '<td><input type="submit" name="do" value="Delete"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="alias.php" method="post">'.
	      '<input type="hidden" name="action" value="addalias">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">';
	print "<tr>\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="label" value="" size="40" maxlength="150"></td>'.
	      '<td><input type="text" name="language" value="" size="2" maxlength="20" placeholder="da"></td>'.
	      '<td><input type="checkbox" name="visible"></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      "</tr>\n";
	print "</form>\n\n";

	print "</table>\n";
} else {
	print "Error: No data id provided.";
}
print "</body>\n</html>\n";

?>
