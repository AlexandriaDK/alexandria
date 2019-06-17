<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'sys';

$system = (int) $_REQUEST['system'];
$action = $_REQUEST['action'];
$name = $_REQUEST['name'];
$description = $_REQUEST['description'];

if (!$action && $system) {
	list($id, $name, $description) = getrow("SELECT id, name, description FROM sys WHERE id = '$system'");
}

if ($action == "ret" && $system) {
	$name = trim($name);
	if (!$name) {
		$_SESSION['admin']['info'] = "Du mangler navnet på systemet!";
	} else {
		$q = "UPDATE sys SET " .
		     "name = '".dbesc($name)."', " .
		     "description = '".dbesc($description)."' ".
		     "WHERE id = '$system'";
		$r = doquery($q);
		if ($r) {
			chlog($system,$this_type,"System rettet");
		}
		$_SESSION['admin']['info'] = "System rettet! " . dberror();
		rexit( $this_type, [ 'system' => $system ] );
	}
}

if ($action == "opret") {
	$name = trim($name);
	$rid = getone("SELECT id FROM sys WHERE name = '$name'");
	if ($rid) {
		$_SESSION['admin']['info'] = "Et system med det navn <a href=\"system.php?system=$rid\">findes allerede</a>";
	} elseif (!$name) {
		$_SESSION['admin']['info'] = "Du mangler navnet på systemet!";
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
		$_SESSION['admin']['info'] = "System oprettet! " . dberror();
		rexit( $this_type, [ 'system' => $system ] );
	}
}

if ($action == "Slet" && $system) {
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
		$_SESSION['admin']['info'] = "System slettet! " . dberror();
		rexit($this_type, ['system' => $system] );
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - system</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<?php
include("links.inc");

printinfo();

print "<FORM ACTION=\"system.php\" METHOD=\"post\">\n";
if (!$system) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"opret\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"system\" value=\"$system\">\n";
}

print "<a href=\"system.php\">Nyt system</a>";

print "<table border=0>\n";

if ($system) {
	print "<tr><td>ID:</td><td>$system - <a href=\"/data?system=$system\" accesskey=\"q\">Vis systemside</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$system\">Vis log</a>";
	}
	print "\n</td></tr>\n";
}

tr("Navn:","name",$name);
print "<tr valign=top><td>Info om systemet:</td><td><textarea name=description cols=60 rows=8 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";


print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($system ? "Ret" : "Opret").' system">' . ($system ? ' <input type="submit" name="action" value="Slet" onclick="return confirm(\'Slet system?\n\nFor en sikkerheds skyld tjekkes der, om alle tilknytninger er fjernet.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

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
	print "<tr valign=top><td align=right>Indeholder følgende<br>scenarier:</td><td>\n";
	foreach($q AS list($id, $title) ) {
		print "<a href=\"scenarie.php?scenarie=$id\">$title</a><br>";
	}
	if (!$q) print "[Ingen]";
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
<big>Vælg system:</big>
</td>

<td>
<select name=system>

<?php
$q = getall("SELECT id, name FROM sys ORDER BY name");
foreach($q AS $r) {
	print "<option value=$r[id]";
	if ($r[id] == $system) print " SELECTED";
	print ">$r[name]\n";
}
?>
</select>
<br>
<input type=submit value="Rediger">

</td>
</tr>
</table>
</form>

</body>
</html>
