<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'aut';

$action = $_REQUEST['action'];
$person = $_REQUEST['person'];
$firstname = $_REQUEST['firstname'];
$surname = $_REQUEST['surname'];
$birth = $_REQUEST['birth'];
$death = $_REQUEST['death'];
$description = $_REQUEST['description'];

if (!$action && $person) {
	$r = getrow("SELECT firstname, surname, description, birth, death FROM aut WHERE id = '$person'");
	if ($r) {
		list($firstname,$surname,$description,$birth,$death) = $r;
	} else {
		unset($person);
	}
}

if ($action == "ret" && $person) {
	if (!$firstname) {
		$_SESSION['admin']['info'] = "Du mangler et navn!";
	} else {
		$q = "UPDATE aut SET " .
		     "firstname = '".dbesc($firstname)."', " .
		     "surname = '".dbesc($surname)."', " .
		     "birth = " . sqlifnull($birth) . ", " .
		     "death = " . sqlifnull($death) . ", " .
		     "description = '".dbesc($description)."' " .
		     "WHERE id = '$person'";
		$r = doquery($q);
		if ($r) {
			chlog($person,$this_type,"Person rettet");
		}
		$_SESSION['admin']['info'] = "Person rettet! " . dberror();
		rexit($this_type, [ 'person' => $person ] );

	}
}
//
// Slet person
//

if ($action == "Slet" && $person) { // burde tjekke om person findes
	$error = [];
	if (getCount('asrel', $person, FALSE, 'aut') ) $error[] = "scenarie";
	if (getCount('acrel', $person, FALSE, 'aut') ) $error[] = "kongres (arrangørposter)";
	if (getCount('trivia', $person, TRUE, 'aut') ) $error[] = "trivia";
	if (getCount('links', $person, TRUE, 'aut') ) $error[] = "link";
	if (getCount('alias', $person, TRUE, 'aut') ) $error[] = "alias";
	if ($error) {
		$_SESSION['admin']['info'] = "Kan ikke slette. Personen har stadigvæk tilknytninger: " . implode(", ",$error);
		rexit($this_type, ['person' => $person] );
	} else {
		$name = getone("SELECT CONCAT(firstname, ' ', surname) AS name FROM aut WHERE id = $person");

		$q = "DELETE FROM aut WHERE id = $person";
		$r = doquery($q);

		if ($r) {
			chlog($person,$this_type,"Person slettet: $name");
		}
		$_SESSION['admin']['info'] = "Person slettet! " . dberror();
		rexit($this_type, ['person' => $person] );
	}
}

if ($action == "opret") {
	$firstname = trim($firstname);
	$surname = trim($surname);
	if (strpos($firstname, " ") !== FALSE && $surname === "") { // extract surname from firstname
		$pos = strrpos($firstname, " ");
		$surname = substr($firstname, $pos+1);
		$firstname = substr($firstname, 0, $pos);
	}
	$rid = getone("SELECT id FROM aut WHERE firstname = '".dbesc($firstname)."' AND surname = '".dbesc($surname)."'");
	if ($rid) {
		$_SESSION['admin']['info'] = "En person med det navn <a href=\"person.php?person=$rid\">findes allerede</a>";
	} elseif (!$firstname) {
		$_SESSION['admin']['info'] = "Du mangler et navn!";
	} else {
		$q = "INSERT INTO aut (id, firstname, surname, description, birth, death) " .
		     "VALUES (NULL, '".dbesc($firstname)."', '".dbesc($surname)."', '".dbesc($description)."', " . sqlifnull($birth) . ", " . sqlifnull($death) .")";
		$r = doquery($q);
		if ($r) {
			$person = dbid();
			chlog($person,$this_type,"Person oprettet");
		}
		$_SESSION['admin']['info'] = "Person oprettet! " . dberror();
		rexit($this_type, [ 'person' => $person ] );
		
	}
}

htmladmstart("Person");

print "<FORM ACTION=\"person.php\" METHOD=\"post\">\n";
if (!$person) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"opret\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"person\" value=\"$person\">\n";
}

print "<a href=\"person.php\">Ny person</a>";

print "<table border=0>\n";

if ($person) {
	print "<tr><td>ID:</td><td>$person - <a href=\"../data?person=$person\" accesskey=\"q\">Vis personside</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$person\">Vis log</a>";
	}
	print "</td></tr>\n";
}

tr("Fornavn:","firstname",$firstname, "", "", "text", TRUE);
tr("Efternavn:","surname",$surname);
print "<tr valign=\"top\"><td>Intern note:</td><td><textarea name=\"description\" cols=50 rows=8 wrap=\"virtual\">\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";
tr("Fødselsdato:", "birth", $birth, "", "ÅÅÅÅ-MM-DD", "date");
tr("Død:","death", $death, "", "ÅÅÅÅ-MM-DD", "date");

/*
$picinfo = "";
if ($picfile) {
	$picpath = "../gfx/userpic/".$picfile;
	if (!file_exists($picpath)) {
		$picinfo = "(filen findes ikke!)";
	} elseif (!$picdata = getimagesize($picpath)) {
		$picinfo = "(ikke en gyldig fil)";
	} else {
		$picinfo = filesize($picpath)." bytes ($picdata[0]x$picdata[1])";
	}
	
}
tr("Evt. billede:","picfile",$picfile, $picinfo);
*/

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($person ? "Ret" : "Opret").' person">' . ($person ? ' <input type="submit" name="action" value="Slet" onclick="return confirm(\'Slet person?\n\nFor en sikkerheds skyld tjekkes der, om alle tilknytninger er fjernet.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($person) {
// Mulighed for at rette links
	print changelinks($person,$this_type);

// Mulighed for at rette trivia
	print changetrivia($person,$this_type);

// Mulighed for at rette alias
	print changealias($person,$this_type);

// Vis evt. billede
	print showpicture($person,$this_type);

// Vis tickets
	print showtickets($person,$this_type);


// Scenarier, personen har medvirket til
	$q = getall("SELECT sce.id, sce.title AS title, title.title AS auttitle FROM sce, asrel, title WHERE sce.id = asrel.sce_id AND asrel.tit_id = title.id AND asrel.aut_id = '$person' ORDER BY title.id, sce.title");
	print "<tr valign=top><td>Scenarier:</td><td>\n";
        foreach($q AS list($id, $title, $auttitle) ) {
		print "<a href=\"scenarie.php?scenarie=$id\">$title</a> ($auttitle)<br>";
	}
	print "</td></tr>\n";
	$q = getall("SELECT convent.id, convent.name, convent.year, acrel.role FROM acrel INNER JOIN convent ON acrel.convent_id = convent.id WHERE acrel.aut_id = '$person' ORDER BY convent.year, convent.begin, convent.end, convent.id");
	print "<tr valign=top><td>Arrangør:</td><td>\n";
        foreach($q AS list($id, $name, $year, $role) ) {
		print "<a href=\"convent.php?con=$id\">$name ($year)</a> ($role)<br>";
	}
	print "</td></tr>\n";
}

?>

</table>

</FORM>

</body>
</html>
