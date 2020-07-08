<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'sys';

$system = (int) $_REQUEST['system'];
$action = (string) $_REQUEST['action'];
$name = (string) $_REQUEST['name'];
$description = (string) $_REQUEST['description'];

if (!$action && $system) {
	list($id, $name, $description) = getrow("SELECT id, name, description FROM sys WHERE id = '$system'");
}

if ($action == "edit" && $system) {
	$name = trim($name);
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "UPDATE sys SET " .
		     "name = '".dbesc($name)."', " .
		     "description = '".dbesc($description)."' ".
		     "WHERE id = '$system'";
		$r = doquery($q);
		if ($r) {
			chlog($system,$this_type,"System rettet");
		}
		$_SESSION['admin']['info'] = "System edited! " . dberror();
		rexit( $this_type, [ 'system' => $system ] );
	}
}

if ($action == "create") {
	$name = trim($name);
	$rid = getone("SELECT id FROM sys WHERE name = '$name'");
	if ($rid) {
		$_SESSION['admin']['info'] = "A system with this name already exists!";
		$_SESSION['admin']['link'] = "system.php?system=" . $rid;
	} elseif (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "INSERT INTO sys (name, description) " .
		     "VALUES ( ".
			 "'".dbesc($name)."', ".
			 "'".dbesc($description)."' ".
			 ")";
		$r = doquery($q);
		if ($r) {
			$system = dbid();
			chlog($system,$this_type,"System oprettet");
		}
		$_SESSION['admin']['info'] = "System created! " . dberror();
		rexit( $this_type, [ 'system' => $system ] );
	}
}

if ($action == "Delete" && $system) {
	$error = [];
	if (getCount('sce', $system, FALSE, 'sys') ) $error[] = "scenarie";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The RPG system still has scenarios registered to it.";
		rexit($this_type, ['system' => $system] );
	} else {
		$name = getone("SELECT name FROM sys WHERE id = $system");

		$q = "DELETE FROM sys WHERE id = $system";
		$r = doquery($q);

		if ($r) {
			chlog($person,$this_type,"System slettet: $name");
		}
		$_SESSION['admin']['info'] = "System deleted! " . dberror();
		rexit($this_type, ['system' => $system] );
	}
}
htmladmstart("System");

print "<FORM ACTION=\"system.php\" METHOD=\"post\">\n";
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
// Mulighed for at rette links
	print changelinks($system,$this_type);

// Mulighed for at rette trivia
	print changetrivia($system,$this_type);

// Mulighed for at rette alias
	print changealias($system,$this_type);

// Vis evt. billede
	print showpicture($system,$this_type);

// Vis tickets
	print showtickets($system,$this_type);

// Scenarier under dette system
	$q = getall("SELECT id, title FROM sce WHERE sys_id = '$system' ORDER BY title, id");
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
$q = getall("SELECT id, name FROM sys ORDER BY name");
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
