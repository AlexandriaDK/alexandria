<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'conset';

$conset = (int) $_REQUEST['conset'];
$action = (string) $_REQUEST['action'];
$name = (string) $_REQUEST['name'];
$description = (string) $_REQUEST['description'];
$internal = (string) $_REQUEST['internal'];
$country = (string) $_REQUEST['country'];
$countryname = getCountryName( $country );

if ( $action ) {
	validatetoken( $token );
}

if (!$action && $conset) {
	$row = getrow("SELECT id, name, description, internal, country FROM conset WHERE id = '$conset'");
	if ($row) {
		$name = $row['name'];
		$description = $row['description'];
		$internal = $row['internal'];
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
		     "internal = '" . dbesc($internal) . "', " .
		     "country = " . sqlifnull($country) . " " .
		     "WHERE id = '$conset'";
		$r = doquery($q);
		if ($r) {
			chlog($conset,$this_type,"Con series updated");
		}
		$_SESSION['admin']['info'] = "Con series updated! " . dberror();
		rexit($this_type, [ 'conset' => $conset ] );
	}
}

if ($action == "create") {
	if (!$name) {
		$_SESSION['admin']['info'] = "Name is missing!";
	} else {
		$q = "INSERT INTO conset (id, name, description, internal, country) " .
		     "VALUES (NULL, '" . dbesc($name) . "', '" . dbesc($description) . "', '" . dbesc($internal) . "', " . sqlifnull($country) . ")";
		$r = doquery($q);
		if ($r) {
			$conset = dbid();
			chlog($conset,$this_type,"Con series created");
		}
		$_SESSION['admin']['info'] = "Con series created! " . dberror();
		rexit($this_type, [ 'conset' => $conset ] );
		
	}
}

htmladmstart("Con series");

print "<FORM ACTION=\"conset.php\" METHOD=\"post\">\n";
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
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

$countryname = getCountryName( $country );

tr("Name","name",$name);
print "<tr valign=top><td>Description</td><td><textarea name=description cols=60 rows=8>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
print "<tr valign=top><td>Internal note</td><td><textarea name=\"internal\" cols=\"60\" rows=\"6\">\n" . stripslashes(htmlspecialchars($internal)) . "</textarea></td></tr>\n";

print '<tr><td>Country code</td><td><input type="text" id="country" name="country" value="' . htmlspecialchars( $country ) . '" placeholder="Two letter ISO code, e.g.: se" size="10"></td><td id="countrynote">' . htmlspecialchars( $countryname ) . '</td></tr>';

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
	$q = getall("SELECT convention.id, convention.name, year, confirmed, conset.name AS setname FROM convention LEFT JOIN conset ON convention.conset_id = conset.id WHERE conset_id = '$conset' ORDER BY setname, year, begin, end, name");
	print "<tr valign=top><td>Contains the following cons</td><td>\n";
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
	if ($r['id'] == $conset) print " SELECTED";
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

<script>
$("#country").change(function() {
	$.get( "lookup.php", { type: 'countrycode', label: $("#country").val() } , function( data ) {
		$("#countrynote").text( data );
	});
});
</script>

</body>
</html>
