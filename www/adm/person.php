<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'aut';

$action = $_REQUEST['action'];
$person = $_REQUEST['person'];
$firstname = $_REQUEST['firstname'];
$surname = $_REQUEST['surname'];
$birth = $_REQUEST['birth'];
$death = $_REQUEST['death'];
$intern = $_REQUEST['intern'];

if ( $action ) {
	validatetoken( $token );
}

if (!$action && $person) {
	$r = getrow("SELECT firstname, surname, intern, birth, death FROM aut WHERE id = '$person'");
	if ($r) {
		list($firstname,$surname,$intern,$birth,$death) = $r;
	} else {
		unset($person);
	}
}

if ($action == "ret" && $person) {
	if (!$firstname) {
		$_SESSION['admin']['info'] = "You are missing a name!";
	} else {
		$q = "UPDATE aut SET " .
		     "firstname = '".dbesc($firstname)."', " .
		     "surname = '".dbesc($surname)."', " .
		     "birth = " . sqlifnull($birth) . ", " .
		     "death = " . sqlifnull($death) . ", " .
		     "intern = '".dbesc($intern)."' " .
		     "WHERE id = '$person'";
		$r = doquery($q);
		if ($r) {
			chlog($person,$this_type,"Person rettet");
		}
		$_SESSION['admin']['info'] = "Person updated! " . dberror();
		rexit($this_type, [ 'person' => $person ] );

	}
}

// Delete person
if ($action == "Delete" && $person) { // burde tjekke om person findes
	$error = [];
	if (getCount('asrel', $person, FALSE, 'aut') ) $error[] = "scenario";
	if (getCount('acrel', $person, FALSE, 'aut') ) $error[] = "con (organizer roles)";
	if (getCount('trivia', $person, TRUE, 'aut') ) $error[] = "trivia";
	if (getCount('links', $person, TRUE, 'aut') ) $error[] = "link";
	if (getCount('alias', $person, TRUE, 'aut') ) $error[] = "alias";
	if (getCount('users', $person, FALSE, 'aut') ) $error[] = "user";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The person still has the following references: " . implode(", ",$error);
		rexit($this_type, ['person' => $person] );
	} else {
		$name = getone("SELECT CONCAT(firstname, ' ', surname) AS name FROM aut WHERE id = $person");

		$q = "DELETE FROM aut WHERE id = $person";
		$r = doquery($q);

		if ($r) {
			chlog($person,$this_type,"Person slettet: $name");
		}
		$_SESSION['admin']['info'] = "Person deleted! " . dberror();
		rexit($this_type, ['person' => $person] );
	}
}

if ($action == "create") {
	$firstname = trim($firstname);
	$surname = trim($surname);
	if (strpos($firstname, " ") !== FALSE && $surname === "") { // extract surname from firstname
		$pos = strrpos($firstname, " ");
		$surname = substr($firstname, $pos+1);
		$firstname = substr($firstname, 0, $pos);
	}
	$rid = getone("SELECT id FROM aut WHERE firstname = '".dbesc($firstname)."' AND surname = '".dbesc($surname)."'");
	if ($rid) {
		$_SESSION['admin']['info'] = "A person with this name <a href=\"person.php?person=$rid\">already exists</a>";
	} elseif (!$firstname) {
		$_SESSION['admin']['info'] = "You are missing a name!";
	} else {
		$q = "INSERT INTO aut (id, firstname, surname, intern, birth, death) " .
		     "VALUES (NULL, '".dbesc($firstname)."', '".dbesc($surname)."', '".dbesc($intern)."', " . sqlifnull($birth) . ", " . sqlifnull($death) .")";
		$r = doquery($q);
		if ($r) {
			$person = dbid();
			chlog($person,$this_type,"Person oprettet");
		}
		$_SESSION['admin']['info'] = "Person created! " . dberror();
		rexit($this_type, [ 'person' => $person ] );
		
	}
}

htmladmstart("Person");

print "<form action=\"person.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
if (!$person) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"person\" value=\"$person\">\n";
}

print "<a href=\"person.php\">New person</a>";

print "<table border=0>\n";

if ($person) {
	print "<tr><td>ID</td><td>$person - <a href=\"../data?person=$person\" accesskey=\"q\">Show page for person</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$person\">Show log</a>";
	}
	print "</td></tr>\n";
}

tr("First name","firstname",$firstname, "", "", "text", TRUE);
tr("Surname","surname",$surname);
print "<tr valign=\"top\"><td>Internal note</td><td><textarea name=\"intern\" cols=50 rows=8 wrap=\"virtual\">\n" . stripslashes(htmlspecialchars($intern)) . "</textarea></td></tr>\n";
tr("Date of birth", "birth", $birth, "", "YYYY-MM-DD", (invaliddate($birth) ? "text" : "date") );
tr("Date of death","death", $death, "", "YYYY-MM-DD", (invaliddate($death) ? "text" : "date") );

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($person ? "Update" : "Create").' person">' . ($person ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete person?\n\nAs a safety mecanism it will be checked if all references are removed.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($person) {
	print changelinks($person,$this_type);
	print changetrivia($person,$this_type);
	print changealias($person,$this_type);
	print showpicture($person,$this_type);
	print showtickets($person,$this_type);

	$q = getall("SELECT sce.id, sce.title AS title, title.title AS auttitle FROM sce, asrel, title WHERE sce.id = asrel.sce_id AND asrel.tit_id = title.id AND asrel.aut_id = '$person' ORDER BY title.id, sce.title");
	print "<tr valign=top><td>Scenarios</td><td>\n";
        foreach($q AS list($id, $title, $auttitle) ) {
		print "<a href=\"game.php?game=$id\">$title</a> ($auttitle)<br>";
	}
	print "</td></tr>\n";
	$q = getall("SELECT convent.id, convent.name, convent.year, acrel.role FROM acrel INNER JOIN convent ON acrel.convent_id = convent.id WHERE acrel.aut_id = '$person' ORDER BY convent.year, convent.begin, convent.end, convent.id");
	print "<tr valign=top><td>Organizer</td><td>\n";
        foreach($q AS list($id, $name, $year, $role) ) {
		print "<a href=\"convent.php?con=$id\">$name ($year)</a> ($role)<br>";
	}
	print "</td></tr>\n";
}
?>
</table>
</form>

</body>
</html>
