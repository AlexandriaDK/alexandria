<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'conset';

$conset = (int) $_REQUEST['conset'];
$action = (string) $_REQUEST['action'];
$name = (string) $_REQUEST['name'];
$description = (string) $_REQUEST['description'];
$intern = (string) $_REQUEST['intern'];
$country = (string) $_REQUEST['intern'];


if (!$action && $conset) {
	$row = getrow("SELECT id, name, description, intern, country FROM conset WHERE id = '$conset'");
	if ($row) {
		$name = $row['name'];
		$description = $row['description'];
		$intern = $row['intern'];
		$country = $row['country'];
	} else {
		unset($conset);
	}
}

if ($action == "edit" && $conset) {
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "UPDATE conset SET " .
		     "name = '" . dbesc($name) . "', " .
		     "description = '" . dbesc($description) . "', " .
		     "intern = '" . dbesc($intern) . "', " .
		     "country = '" . dbesc($country) . "' " .
		     "WHERE id = '$conset'";
		$r = doquery($q);
		if ($r) {
			chlog($conset,$this_type,"Conset rettet");
		}
		$_SESSION['admin']['info'] = "Con series edited! " . dberror();
		rexit($this_type, [ 'conset' => $conset ] );
	}
}

if ($action == "create") {
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "INSERT INTO conset (id, name, description, intern, country) " .
		     "VALUES (NULL, '" . dbesc($name) . "', '" . dbesc($description) . "', '" . dbesc($intern) . "', '" . dbesc($country) . "')";
		$r = doquery($q);
		if ($r) {
			$conset = dbid();
			chlog($conset,$this_type,"Conset oprettet");
		}
		$_SESSION['admin']['info'] = "Con series created! " . dberror();
		rexit($this_type, [ 'conset' => $conset ] );
		
	}
}

htmladmstart("Con series");

printinfo();

print "<FORM ACTION=\"conset.php\" METHOD=\"post\">\n";
if (!$conset) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"edit\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"conset\" value=\"$conset\">\n";
}

print "<a href=\"conset.php\">New con series</a>";

print "<table border=0>\n";

if ($conset) {
	print "<tr><td>ID</td><td>$conset - <a href=\"../data?conset=$conset\" accesskey=\"q\">Show con series page</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$conset\">Show log</a>";
	}
	print "\n</td></tr>\n";
}

tr("Name","name",$name);
print "<tr valign=top><td>Description</td><td><textarea name=description cols=60 rows=8>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
print "<tr valign=top><td>Internal note</td><td><textarea name=\"intern\" cols=\"60\" rows=\"6\">\n" . stripslashes(htmlspecialchars($intern)) . "</textarea></td></tr>\n";

tr("Country code","country", $country, "", "Two letter ISO code, e.g.: dk" );

$ror = ($conset) ? "Update" : "Create";
?>

<tr><td></td><td><INPUT TYPE="submit" VALUE="<?php print $ror; ?> con series"></td></tr>

<?php

if ($conset) {
	print changelinks($conset,$this_type);
	print changetrivia($conset,$this_type);
	print changealias($conset,$this_type);
	print changefiles($conset,$this_type);
	print showtickets($conset,$this_type);

// Afholdte con'er
	$q = getall("SELECT convent.id, convent.name, year, confirmed, conset.name AS setname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE conset_id = '$conset' ORDER BY setname, year, begin, end, name");
	print "<tr valign=top><td>Contains the following cons:</td><td>\n";
        foreach($q AS list($id, $name, $y, $c) ){
		if ($c == 0) $conftext = "(missing scenarios)";
		elseif ($c == 1) $conftext = "(being edited)";
		else $conftext = "";
		print "<a href=\"convent.php?con=$id\">$name ($y)</a> $conftext<br>";
	}
	print "</td></tr>\n";
}

?>

</table>

</FORM>

<hr size=1>

<form action="conset.php" method=get>
<table border=0>
<tr>
<td>
<big>Select con series</big>
</td>

<td>
<select name=conset>

<?php
$q = getall("SELECT id, name FROM conset ORDER BY name");
foreach($q AS $r) {
	print "<option value=$r[id]";
	if ($r[id] == $conset) print " SELECTED";
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
