<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$this_type = 'convent';

$con = (int) $_REQUEST['con'];
$action = $_REQUEST['action'];
$name = $_REQUEST['name'];
$year = $_REQUEST['year'];
$begin = $_REQUEST['begin'];
$end = $_REQUEST['end'];
$place = $_REQUEST['place'];
$conset_id = $_REQUEST['conset_id'];
$description = $_REQUEST['description'];
$intern = $_REQUEST['intern'];
$confirmed = $_REQUEST['confirmed'];

if (!$action && $con) {
	$row = getrow("SELECT id, name, year, begin, end, place, conset_id, description, intern, confirmed FROM convent WHERE id = '$con'");
	if ($row) {
		$name = $row['name'];
		$year = $row['year'];
		$begin = $row['begin'];
		$end = $row['end'];
		$place = $row['place'];
		$conset_id = $row['conset_id'];
		$description = $row['description'];
		$intern = $row['intern'];
		$confirmed = $row['confirmed'];
		$qq = getall("
			SELECT id, name, year, begin, end
			FROM convent 
			WHERE conset_id = '$conset_id'
			ORDER BY year, begin, name
		");
		$seriedata = [];
		foreach($qq AS $row) {
			$seriecount++;
			$seriedata['id'][$seriecount] = $row['id'];
			$seriedata['name'][$seriecount] = $row['name'];
			$seriedata['year'][$seriecount] = $row['year'];
			$seriedata['begin'][$seriecount] = $row['begin'];
			$seriedata['end'][$seriecount] = $row['end'];
			if ($row['id'] == $con) $seriethis = $seriecount;
		}
		$con_prev = $seriedata['id'][($seriethis-1)];
		$con_next = $seriedata['id'][($seriethis+1)];
	} else {
		unset($con);
	}
}

if ($action == "ret" && $con) {
	if (!$name) {
		$_SESSION['admin']['info'] = "Du mangler et navn!";
	} else {
		$year = intval($year);
		$year = ($year > 1950 && $year < 2050) ? "'$year'" : "NULL";
		$q = "UPDATE convent SET " .
		     "name = '".dbesc($name)."', " .
		     "year = $year, " .
		     "begin = " . sqlifnull($begin) . ", " .
		     "end = " . sqlifnull($end) . ", " .
		     "place = '".dbesc($place)."', " .
		     "description = '".dbesc($description)."', ".
		     "intern = '".dbesc($intern)."', ".
		     "conset_id = '".dbesc($conset_id)."', " .
		     "confirmed = '".dbesc($confirmed)."' " .
		     "WHERE id = '$con'";
		$r = query($q);
		print dberror();
		if ($r) {
			chlog($con,$this_type,"Con rettet");
		}
		$_SESSION['admin']['info'] = "Con rettet! " . dberror();
		rexit($this_type, ['con' => $con] );
	}
}

//
// Slet kongres
//

if ($action == "Slet" && $con) { // burde tjekke om kongres findes
	$error = [];
	if (getCount('csrel', $con, FALSE, 'convent') ) $error[] = "kongres";
	if (getCount('acrel', $con, FALSE, 'convent') ) $error[] = "kongres (arrangørposter)";
	if (getCount('trivia', $con, TRUE, 'convent') ) $error[] = "trivia";
	if (getCount('links', $con, TRUE, 'convent') ) $error[] = "link";
	if (getCount('alias', $con, TRUE, 'convent') ) $error[] = "alias";
	if (getCount('files', $con, TRUE, 'convent') ) $error[] = "files";
	if (getCount('userlog', $con, TRUE, 'convent') ) $error[] = "brugerlog (kræver admin)";
	if ($error) {
		$_SESSION['admin']['info'] = "Kan ikke slette. Kongressen har stadigvæk tilknytninger: " . implode(", ",$error);
		rexit($this_type, ['con' => $con] );
	} else {
		$name = getone("SELECT CONCAT(name, ' ', year) FROM convent WHERE id = $con");

		$q = "DELETE FROM convent WHERE id = $con";
		$r = doquery($q);

		if ($r) {
			chlog($con,$this_type,"Con slettet: $name");
		}
		$_SESSION['admin']['info'] = "Kongres slettet! " . dberror();
		rexit($this_type, ['con' => $con] );
	}
}


if ($action == "opret") {
	if (!$name) {
		$_SESSION['admin']['info'] = "Du mangler et navn på con'en!";
	} else {
		$year = intval($year);
		$year = ($year > 1950 && $year < 2050) ? "'$year'" : "NULL";
		$q = "INSERT INTO convent (id, name, year, begin, end, place, conset_id, description, intern, confirmed) " .
		     "VALUES (NULL, ".
			 "'".dbesc($name)."', ".
			 "$year, ".
			 "'".dbesc($begin)."', ".
			 "'".dbesc($end)."', ".
			 "'".dbesc($place)."', ".
			 "'".dbesc($conset_id)."', ".
			 "'".dbesc($description)."', ".
			 "'".dbesc($intern)."', ".
			 "'".dbesc($confirmed)."'".
			 ")";
		$r = query($q);
		if ($r) {
			$con = dbid();
			chlog($con,$this_type,"Con oprettet");
		}
		$_SESSION['admin']['info'] = "Con oprettet! " . dberror();
		rexit($this_type, [ 'con' => $con ] );
	}
}

unset($conset);
$conset[0] = "[ingen eller ukendt con-række]";
$q = getall("SELECT id, name FROM conset ORDER BY name");
foreach($q AS $r) {
	$conset[$r[id]] = $r[name];
}

$conflist = array(
	0 => "Scenarieliste mangler",
	1 => "Scenarieliste under indtastning",
	2 => "Scenarieliste komplet jf. program",
	9 => "Scenarieliste bekræftet"
);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - con</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<!--
<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="./" style="text-decoration: none">Con-administration</a></i></font>
</p>
-->

<?php
include("links.inc");

printinfo();

print "<FORM ACTION=\"convent.php\" METHOD=\"post\">\n";
if (!$con) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"opret\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"con\" value=\"$con\">\n";
}

print "<a href=\"convent.php\">Ny con</a>";

print "<table border=0>\n";

if ($con) {
	print "<tr><td>ID:</td><td>$con - <a href=\"/data?con=$con\" accesskey=\"q\">Vis con-side</a> - <a href=\"lock.php?con=$con\">Brug con som default con</a>";
	if($con_prev) {
		print " - <a href=\"convent.php?con=".$con_prev."\">Forrige</a>";
	}
	if($con_next) {
		print " - <a href=\"convent.php?con=".$con_next."\">Næste</a>";
	}
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$con\">Vis log</a>";
	}
#	$next = $con + 1;
#	print " - <a href=\"convent.php?con=$next\">Næste ID</a>";
	print "\n</td></tr>\n";
}

tr("Navn:","name",$name);
tr("Årstal:","year",$year, "", "ÅÅÅÅ","number");
#print "<tr valign=top><td>Info om con'en:</td><td><textarea name=description cols=60 rows=8 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
if ($begin && $begin != "0000-00-00") {
	list($y,$m,$d) = explode("-",$begin);
	$opta = "($d/$m $y = ". $ugedag[date("w",mktime(0,0,0,$m,$d,$y))] . ")";
}

if ($end && $end != "0000-00-00") {
	list($y,$m,$d) = explode("-",$end);
	$optb = "($d/$m $y = ". $ugedag[date("w",mktime(0,0,0,$m,$d,$y))] . ")";
}

tr("Startdato:","begin",$begin,$opta, "ÅÅÅÅ-MM-DD","date");
tr("Slutdato:","end",$end,$optb, "ÅÅÅÅ-MM-DD","date");

tr("Sted:","place",$place);

print "<tr valign=top><td>Info om connen:</td><td><textarea name=description cols=60 rows=8 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
print "<tr valign=top><td>Intern note:</td><td><textarea name=intern cols=60 rows=4 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($intern)) . "</textarea></td></tr>\n";


## Con-serie ##

print "<tr valign=top><td>Con-serie:</td>";
print "<td>\n";
print "<select name=\"conset_id\">\n";

foreach ($conset as $id => $name) {
	print "<option value=$id";
	if ($id == $conset_id) print " SELECTED";
	print ">$name\n";
}
print "</select>\n";
print "</td></tr>\n\n";

## Bekræft data? ##

print "<tr valign=top><td>Datavaliditet:</td>";
print "<td>\n";
print "<select name=\"confirmed\">\n";

foreach ($conflist AS $id => $name) {
	print "<option value=$id";
	if ($id == $confirmed) print " SELECTED";
	print ">$name\n";
}
print "</select>\n";
print "</td></tr>\n\n";

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($con ? "Ret" : "Opret").' con">' . ($con ? ' <input type="submit" name="action" value="Slet" onclick="return confirm(\'Slet kongres?\n\nFor en sikkerheds skyld tjekkes der, om alle tilknytninger er fjernet.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

## Links og scenarier i con ##

if ($con) {
// Mulighed for at rette links
	print changelinks($con,$this_type);

// Mulighed for at rette trivia
	print changetrivia($con,$this_type);

// Mulighed for at rette alias
	print changealias($con,$this_type);

// Mulighed for at rette alias
	print changeorganizers($con,$this_type);

// Mulighed for at rette filer
	print changefiles($con,$this_type);

// Mulighed for at rette priser for kongressen
	print changeawards($con);

// Hvor mange personer har markeret kongressen i deres log?
	print changeuserlog($con,$this_type);

// Vis evt. billede
	print showpicture($con,$this_type);

// Vis tickets
	print showtickets($con,$this_type);

// Scenarier under con'en	
	$q = getall("SELECT sce.id, title, pre.id AS preid, event FROM sce, csrel, pre WHERE csrel.convent_id = '$con' AND csrel.sce_id = sce.id AND csrel.pre_id = pre.id ORDER BY title");
	print dberror();
	print "<tr valign=top><td>Scenarier herunder:</td><td>\n";
	
        foreach($result AS list($id, $title, $preid, $event) ) {
		print "<a href=\"scenarie.php?scenarie=$id\">$title</a>";
		if ($preid > 1) print " ($event)";
		print "<br>";
	}
	
	print "</td></tr>\n";
}



?>

</table>

</FORM>

<hr size=1>

<form action="convent.php" method=get>
<table border=0>
<tr valign=baseline>
<td>
<big>Vælg con:</big>
</td>

<td>
<!--
<select name=con onChange="form.submit()">
-->
<select name=con>

<?php
$q = getall("SELECT convent.id, convent.name, year, conset.name AS setname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id ORDER BY setname, year, begin, name");

foreach($q AS $r) {
	print "<option value=$r[id]";
	if ($r[id] == $con) print " SELECTED";
	print ">$r[name] ($r[year])\n";
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
