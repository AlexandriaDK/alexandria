<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'conset';

$conset = (int) $_REQUEST['conset'];
$action = $_REQUEST['action'];
$name = $_REQUEST['name'];
$description = $_REQUEST['description'];
$intern = $_REQUEST['intern'];


if (!$action && $conset) {
	$row = getrow("SELECT id, name, description, intern FROM conset WHERE id = '$conset'");
	if ($row) {
		$name = $row['name'];
		$description = $row['description'];
		$intern = $row['intern'];
	} else {
		unset($conset);
	}
}

if ($action == "ret" && $conset) {
	if (!$name) {
		$_SESSION['admin']['info'] = "Du mangler et navn!";
	} else {
		$q = "UPDATE conset SET " .
		     "name = '" . dbesc($name) . "', " .
		     "description = '" . dbesc($description) . "', " .
		     "intern = '" . dbesc($intern) . "' " .
		     "WHERE id = '$conset'";
		$r = doquery($q);
		if ($r) {
			chlog($conset,$this_type,"Conset rettet");
		}
		$_SESSION['admin']['info'] = "Con-serie rettet! " . dberror();
		rexit($this_type, [ 'conset' => $conset ] );
	}
}

if ($action == "opret") {
	if (!$name) {
		$_SESSION['admin']['info'] = "Du mangler et navn på con-serien!";
	} else {
		$q = "INSERT INTO conset (id, name, description, intern) " .
		     "VALUES (NULL, '" . dbesc($name) . "', '" . dbesc($description) . "', '" . dbesc($intern) . "')";
		$r = doquery($q);
		if ($r) {
			$conset = dbid();
			chlog($conset,$this_type,"Conset oprettet");
		}
		$_SESSION['admin']['info'] = "Con-serie oprettet! " . dberror();
		rexit($this_type, [ 'conset' => $conset ] );
		
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - con-set</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<?php
include("links.inc");

printinfo();

print "<FORM ACTION=\"conset.php\" METHOD=\"post\">\n";
if (!$conset) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"opret\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"conset\" value=\"$conset\">\n";
}

print "<a href=\"conset.php\">Ny con-serie</a>";

print "<table border=0>\n";

if ($conset) {
	print "<tr><td>ID:</td><td>$conset - <a href=\"../data?conset=$conset\" accesskey=\"q\">Vis con-serie</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$conset\">Vis log</a>";
	}
	print "\n</td></tr>\n";
}

tr("Navn:","name",$name);
print "<tr valign=top><td>Info om con-serien:</td><td><textarea name=description cols=60 rows=8 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
print "<tr valign=top><td>Intern note:</td><td><textarea name=\"intern\" cols=\"60\" rows=\"6\">\n" . stripslashes(htmlspecialchars($intern)) . "</textarea></td></tr>\n";


$ror = ($conset) ? "Ret" : "Opret";

?>

<tr><td>&nbsp;</td><td><INPUT TYPE="submit" VALUE="<?php print $ror; ?> con-serie"></td></tr>

<?php

if ($conset) {
	print changelinks($conset,$this_type);
	print changetrivia($conset,$this_type);
	print changealias($conset,$this_type);
	print changefiles($conset,$this_type);
	print showtickets($conset,$this_type);

// Afholdte con'er
	$q = getall("SELECT convent.id, convent.name, year, confirmed, conset.name AS setname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE conset_id = '$conset' ORDER BY setname, year, begin, end, name");
	print "<tr valign=top><td>Indeholder følgende kongresser:</td><td>\n";
        foreach($q AS list($id, $name, $y, $c) ){
		if ($c == 0) $conftext = "(mangler scenarier)";
		elseif ($c == 1) $conftext = "(under indtastning)";
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
<tr valign=baseline>
<td>
<big>Vælg con-serie:</big>
</td>

<td>
<!--
<select name=conset onChange="form.submit()">
-->
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
<input type=submit value="Rediger">

</td>
</tr>
</table>
</form>

</body>
</html>
