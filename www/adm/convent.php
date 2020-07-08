<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$this_type = 'convent';

$con = (int) $_REQUEST['con'];
$action = $_REQUEST['action'];
$name = trim( (string) $_REQUEST['name'] );
$year = $_REQUEST['year'];
$begin = $_REQUEST['begin'];
$end = $_REQUEST['end'];
$place = trim( (string) $_REQUEST['place'] );
$conset_id = $_REQUEST['conset_id'];
$description = ltrim( (string) $_REQUEST['description']);
$intern = $_REQUEST['intern'];
$confirmed = $_REQUEST['confirmed'];
$country = trim( (string) $_REQUEST['country'] );
$cancelled = (int) (bool) $_REQUEST['cancelled'];

if (!$action && $con) {
	$row = getrow("SELECT a.id, a.name, a.year, a.begin, a.end, a.place, a.conset_id, a.description, a.intern, a.confirmed, a.country, b.country AS cscountry, a.cancelled FROM convent a LEFT JOIN conset b ON a.conset_id = b.id WHERE a.id = $con");
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
		$country = $row['country'];
		$cscountry = $row['cscountry'];
		$cancelled = $row['cancelled'];
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

if ($action == "edit" && $con) {
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
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
		     "country = ".sqlifnull($country).", " .
		     "cancelled = '".dbesc($cancelled)."', " .
		     "confirmed = '".dbesc($confirmed)."' " .
		     "WHERE id = '$con'";
		$r = doquery($q);
		print dberror();
		if ($r) {
			chlog($con,$this_type,"Con rettet");
		}
		$_SESSION['admin']['info'] = "Con edited! " . dberror();
		rexit($this_type, ['con' => $con] );
	}
}

//
// Delete con
//

if ($action == "Delete" && $con) { // burde tjekke om kongres findes
	$error = [];
	if (getCount('csrel', $con, FALSE, 'convent') ) $error[] = "scenario";
	if (getCount('acrel', $con, FALSE, 'convent') ) $error[] = "con (organizer)";
	if (getCount('trivia', $con, TRUE, 'convent') ) $error[] = "trivia";
	if (getCount('links', $con, TRUE, 'convent') ) $error[] = "link";
	if (getCount('alias', $con, TRUE, 'convent') ) $error[] = "alias";
	if (getCount('files', $con, TRUE, 'convent') ) $error[] = "files";
	if (getCount('userlog', $con, TRUE, 'convent') ) $error[] = "user log (requires admin access)";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The congress still has relations: " . implode(", ",$error);
		rexit($this_type, ['con' => $con] );
	} else {
		$name = getone("SELECT CONCAT(name, ' ', year) FROM convent WHERE id = $con");

		$q = "DELETE FROM convent WHERE id = $con";
		$r = doquery($q);

		if ($r) {
			chlog($con,$this_type,"Con slettet: $name");
		}
		$_SESSION['admin']['info'] = "Con deleted! " . dberror();
		rexit($this_type, ['con' => $con] );
	}
}


if ($action == "create") {
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$year = intval($year);
		$year = ($year > 1950 && $year < 2050) ? "'$year'" : "NULL";
		$q = "INSERT INTO convent (id, name, year, begin, end, place, conset_id, description, intern, confirmed, cancelled, country) " .
		     "VALUES (NULL, ".
			 "'".dbesc($name)."', ".
			 "$year, ".
			 "'".dbesc($begin)."', ".
			 "'".dbesc($end)."', ".
			 "'".dbesc($place)."', ".
			 "'".dbesc($conset_id)."', ".
			 "'".dbesc($description)."', ".
			 "'".dbesc($intern)."', ".
			 "'".dbesc($confirmed)."',".
			 "'".dbesc($cancelled)."',".
			 sqlifnull($country).
			 ")";
		$r = doquery($q);
		if ($r) {
			$con = dbid();
			chlog($con,$this_type,"Con oprettet");
		}
		$_SESSION['admin']['info'] = "Con created " . dberror();
		rexit($this_type, [ 'con' => $con ] );
	}
}

unset($conset);
$q = getall("SELECT id, name FROM conset ORDER BY id != 40, name");
foreach($q AS $r) {
	$conset[$r['id']] = $r['name'];
}

$conflist = array(
	0 => [ "label" => "Missing list of games", "level" => 1],
	1 => [ "label" => "List of games available", "level" => 2],
	2 => [ "label" => "Games currently being entered", "level" => 3],
	3 => [ "label" => "Games entered, descriptions available", "level" => 2],
	4 => [ "label" => "Descriptions currently being entered", "level" => 3],
	5 => [ "label" => "Stuff still missing (write under internal note)", "level" => 2],
	6 => [ "label" => "Program data entered", "level" => 4],
	9 => [ "label" => "List confirmed", "level" => 4]
);

htmladmstart("Con");

print "<FORM ACTION=\"convent.php\" METHOD=\"post\">\n";
if (!$con) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"edit\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"con\" value=\"$con\">\n";
}

print "<a href=\"convent.php\">New con</a>";

print "<table border=0>\n";

if ($con) {
	print "<tr><td>ID</td><td>$con - <a href=\"../data?con=$con\" accesskey=\"q\">Show con page</a> - <a href=\"lock.php?con=$con\">Use con as default con</a>";
	if($con_prev) {
		print " - <a href=\"convent.php?con=".$con_prev."\">Previous</a>";
	}
	if($con_next) {
		print " - <a href=\"convent.php?con=".$con_next."\">Next</a>";
	}
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$con\">Show log</a>";
	}
	print "\n</td></tr>\n";
}

tr("Name","name",$name);
tr("Year","year",$year, "", "","number");
if ($begin && $begin != "0000-00-00") {
	list($y,$m,$d) = explode("-",$begin);
	$opta = "($d/$m $y = ". $ugedag[date("w",mktime(0,0,0,$m,$d,$y))] . ")";
}

if ($end && $end != "0000-00-00") {
	list($y,$m,$d) = explode("-",$end);
	$optb = "($d/$m $y = ". $ugedag[date("w",mktime(0,0,0,$m,$d,$y))] . ")";
}

$countryname = getCountryName( ($country ? $country : $cscountry) );

tr("Start date","begin",$begin,$opta, "YYYY-MM-DD","date");
tr("End date","end",$end,$optb, "YYYY-MM-DD","date");

tr("Location","place",$place);
print '<tr><td>Country code</td><td><input type="text" id="country" name="country" value="' . htmlspecialchars( $country ) . '" placeholder="Two letter ISO code, e.g.: se" size="50"></td><td id="countrynote">' . htmlspecialchars($cscountry ? $cscountry . " - " . $countryname . " (derived from con series - no need to enter)" : $countryname)  . '</td></tr>';

print "<tr valign=top><td>Description</td><td><textarea name=description cols=60 rows=8 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
print "<tr valign=top><td>Internal note</td><td><textarea name=intern cols=60 rows=4 WRAP=VIRTUAL>\n" . stripslashes(htmlspecialchars($intern)) . "</textarea></td></tr>\n";


## Con-serie ##

print "<tr valign=top><td>Con series</td>";
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

print "<tr valign=top><td>Data validity</td>";
print "<td>\n";
print "<select name=\"confirmed\">\n";

foreach ($conflist AS $id => $stage) {
	$levelcolors = [ 1 => '#d55', 2 => '#d95', 3 => '#dd5', 4 => '#5d5' ];
	print "<option value=$id";
	if ($id == $confirmed) print " SELECTED";
	print " style=\"background-color: " . $levelcolors[$stage['level']] . "\"";
	
	print ">" . $stage['label'] . "\n";
}
print "</select>\n";
print "</td></tr>\n\n";

print "<tr valign=top><td>Cancelled?</td>";
print "<td>\n";
print "<input type=\"checkbox\" name=\"cancelled\" " . ($cancelled ? "checked=\"checked\"" : "") . "/>\n";
print "</td>\n";
print "</tr>\n\n";

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($con ? "Edit" : "Create").' con">' . ($con ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete con?\n\nAs a precaution every relation will be checked.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

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
	print "<tr valign=top><td>Scenarios connected</td><td>\n";
	
        foreach($q AS list($id, $title, $preid, $event) ) {
		if ($title == "") {
			$title = "(error - no title)";
		}
		print "<a href=\"game.php?game=$id\">$title</a>" . PHP_EOL;
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
<big>Select con</big>
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
	if ($r['id'] == $con) print " SELECTED";
	print ">$r[name] ($r[year])\n";
}
?>
</select>
<br>
<input type=submit value="Edit">

</td>
</tr>
</table>
</form>

<script>
$("#country").change(function() {
	$.get( "lookup.php", { type: 'countrycode', label: $("#country").val() } , function( data ) {
		$("#countrynote").text( data );
	});
});
</script>

</body>
</html>
