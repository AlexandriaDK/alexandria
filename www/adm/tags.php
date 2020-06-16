<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'tags';

$action = (string) $_REQUEST['action'];
$do = (string) $_REQUEST['do'];
$tag = (string) $_REQUEST['tag'];
$id = (int) $_REQUEST['id'];
$category = (string) $_REQUEST['category'];
$data_id = (int) $_REQUEST['data_id'];
if ($category == 'game') $category = 'sce';

// Update tag
if ($action == "changetag" && $do != "Remove") {
	$url = trim($url);
	$description = trim($description);
	$q = "UPDATE tags SET " .
	     "tag = '" . dbesc($tag) . "' " .
	     "WHERE id = $id";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Tag rettet til $tag");
	}
	$_SESSION['admin']['info'] = "Tag updated! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Remove tag
if ($action == "changetag" && $do == "Remove") {
	$tag = getone("SELECT tag FROM tags WHERE id = $id");
	$q = "DELETE FROM tags WHERE id = $id";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Tag slettet: $tag");
	}
	$_SESSION['admin']['info'] = "Tag removed! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Add tag
if ($action == "addtag" && $tag != "") {
	$q = "INSERT INTO tags " .
	     "(sce_id, tag) VALUES ".
	     "($data_id, '". dbesc($tag) . "')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id,$category,"Tag oprettet: $tag");
	}
	$_SESSION['admin']['info'] = "Tag added! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );

}


if ($data_id && $category) {
	$data_id = intval($data_id);
	switch($category) {
	case 'aut':
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
		break;
	case 'sce':
		$cat = 'sce';
		$q = "SELECT title FROM sce WHERE id = '$data_id'";
		$mainlink = "game.php?game=$data_id";
		break;
	case 'convent':
		$cat = 'convent';
		$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
		$mainlink = "convent.php?con=$data_id";
		break;
	case 'conset':
		$cat = 'conset';
		$q = "SELECT name FROM conset WHERE id = '$data_id'";
		$mainlink = "conset.php?conset=$data_id";
		break;
	case 'sys':
		$cat = 'sys';
		$q = "SELECT name FROM sys WHERE id = '$data_id'";
		$mainlink = "system.php?system=$data_id";
		break;
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, tag FROM tags WHERE sce_id = $data_id ORDER BY tag";
	$result = getall($query);
}

htmladmstart("Tags");

if ($data_id && $category) {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Edit tags for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Tag</th>".
	      "</tr>\n";

	foreach($result AS $row) {
		print '<form action="tags.php" method="post">'.
		      '<input type="hidden" name="action" value="changetag">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="text" name="tag" value="'.htmlspecialchars($row['tag']).'" size=40 maxlength=100></td>'.
		      '<td><input type="submit" name="do" value="Update"></td>'.
		      '<td><input type="submit" name="do" value="Remove"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="tags.php" method="post">'.
	      '<input type="hidden" name="action" value="addtag">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">';
	print "<tr>\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="tag" value="" size=40 maxlength=100></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Add"></td>'.
	      "</tr>\n";
	print "</form>\n\n";

	print "</table>\n";
} else {
	print "Error: No data id provided.";
}
print "</body>\n</html>\n";

?>
