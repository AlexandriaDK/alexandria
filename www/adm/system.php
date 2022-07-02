<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'sys';
$this_type_new = 'system';

$system = (int) ($_REQUEST['system'] ?? '');
$action = (string) ($_REQUEST['action'] ?? '');
$name = (string) ($_REQUEST['name'] ?? '');
$description = (string) ($_REQUEST['description'] ?? '');

$this_id = $system;

if ( $action ) {
	validatetoken( $token );
}


if (!$action && $system) {
	list($id, $name, $description) = getrow("SELECT id, name, description FROM gamesystem WHERE id = '$system'");
}

if ($action == "edit" && $system) {
	$name = trim($name);
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "UPDATE gamesystem SET " .
		     "name = '".dbesc($name)."', " .
		     "description = '".dbesc($description)."' ".
		     "WHERE id = '$system'";
		$r = doquery($q);
		if ($r) {
			chlog($system,$this_type,"System edited");
		}
		$_SESSION['admin']['info'] = "System edited! " . dberror();
		rexit( $this_type, [ 'system' => $system ] );
	}
}

if ($action == "create") {
	$name = trim($name);
	$rid = getone("SELECT id FROM gamesystem WHERE name = '$name'");
	if ($rid) {
		$_SESSION['admin']['info'] = "A system with this name already exists!";
		$_SESSION['admin']['link'] = "system.php?system=" . $rid;
	} elseif (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "INSERT INTO gamesystem (name, description) " .
		     "VALUES ( ".
			 "'".dbesc($name)."', ".
			 "'".dbesc($description)."' ".
			 ")";
		$r = doquery($q);
		if ($r) {
			$system = dbid();
			chlog($system,$this_type,"System created");
		}
		$_SESSION['admin']['info'] = "System created! " . dberror();
		rexit( $this_type, [ 'system' => $system ] );
	}
}

if ($action == "Delete" && $system) {
	$error = [];
	if (getCount('game', $this_id, FALSE, $this_type_new) ) $error[] = "game";
	if (getCount('article_reference', $this_id, TRUE, $this_type_new) ) $error[] = "article reference";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The tag still has relations: " . implode(", ",$error);
		rexit($this_type, ['system' => $system] );
	} else {
		$name = getone("SELECT name FROM gamesystem WHERE id = $this_id");

		$q = "DELETE FROM gamesystem WHERE id = $system";
		$r = doquery($q);

		if ($r) {
			chlog($this_id,$this_type,"System deleted: $name");
		}
		$_SESSION['admin']['info'] = "System deleted! " . dberror();
		rexit($this_type);
	}
}
htmladmstart("System");

print "<FORM ACTION=\"system.php\" METHOD=\"post\">\n";
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
if (!$system) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"edit\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"system\" value=\"$system\">\n";
}

print "<a href=\"system.php\">New system</a>";

print "<table border=0>\n";

if ($system) {
	print "<tr><td>ID</td><td>$system - <a href=\"../data?system=$system\" accesskey=\"q\">Show RPG system page</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$system\">Show log</a>";
	}
	print "\n</td></tr>\n";
}

tr("Name","name",$name);
print "<tr valign=top><td>Description</td><td><textarea name=description cols=60 rows=8>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";


print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($system ? "Update" : "Create").' system">' . ($system ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete system?\n\nAs a safety precaution all relations will be checked.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($system) {
	print changelinks($system,$this_type);
	print changetrivia($system,$this_type);
	print changealias($system,$this_type);
	print changefiles($system,$this_type);
	print showpicture($system,$this_type);
	print showtickets($system,$this_type);

	$q = getall("SELECT id, title FROM game WHERE gamesystem_id = '$system' ORDER BY title, id");
	print "<tr valign=top><td align=right>Contains the following<br>scenarios</td><td>\n";
	foreach($q AS list($id, $title) ) {
		print "<a href=\"game.php?game=$id\">$title</a><br>";
	}
	if (!$q) print "[None]";
	print "</td></tr>\n";
}

?>

</table>

</form>

<hr size=1>

<form action="system.php" method="get">
<table border=0>
<tr valign="baseline">
<td>
<big>Choose system</big>
</td>

<td>
<select name=system>

<?php
$q = getall("SELECT id, name FROM gamesystem ORDER BY name");
foreach($q AS $r) {
	print "<option value=$r[id]";
	if ($r['id'] == $system) print " SELECTED";
	print ">$r[name]\n";
}
?>
</select>
<br>
<input type=submit value="Edit">

</td>
</tr>
</table>
</form>

</body>
</html>
