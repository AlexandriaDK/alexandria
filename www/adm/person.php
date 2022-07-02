<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'aut';
$this_type_new = 'person';

$action = $_REQUEST['action'] ?? '';
$person = $_REQUEST['person'] ?? '';
$firstname = $_REQUEST['firstname'] ?? '';
$surname = $_REQUEST['surname'] ?? '';
$birth = $_REQUEST['birth'] ?? '';
$death = $_REQUEST['death'] ?? '';
$internal = $_REQUEST['internal'] ?? '';

$this_id = $person;

if ( $action ) {
	validatetoken( $token );
}

if (!$action && $person) {
	$r = getrow("SELECT firstname, surname, internal, birth, death FROM person WHERE id = '$person'");
	if ($r) {
		list($firstname,$surname,$internal,$birth,$death) = $r;
	} else {
		unset($person);
	}
}

if ($action == "ret" && $person) {
	if (!$firstname) {
		$_SESSION['admin']['info'] = "You are missing a name!";
	} else {
		$q = "UPDATE person SET " .
		     "firstname = '".dbesc($firstname)."', " .
		     "surname = '".dbesc($surname)."', " .
		     "birth = " . sqlifnull($birth) . ", " .
		     "death = " . sqlifnull($death) . ", " .
		     "internal = '".dbesc($internal)."' " .
		     "WHERE id = '$person'";
		$r = doquery($q);
		if ($r) {
			chlog($person,$this_type,"Person updated");
		}
		$_SESSION['admin']['info'] = "Person updated! " . dberror();
		rexit($this_type, [ 'person' => $person ] );

	}
}

// Delete person
if ($action == "Delete" && $person) { // Should check if $person id exists
	$error = [];
	if (getCount('pgrel', $this_id, FALSE, $this_type_new) ) $error[] = "scenario";
	if (getCount('pcrel', $this_id, FALSE, $this_type_new) ) $error[] = "con (organizer roles)";
	if (getCount('trivia', $this_id, FALSE, $this_type_new) ) $error[] = "trivia";
	if (getCount('links', $this_id, FALSE, $this_type_new) ) $error[] = "link";
	if (getCount('alias', $this_id, FALSE, $this_type_new) ) $error[] = "alias";
	if (getCount('users', $this_id, FALSE, $this_type) ) $error[] = "user";
	if (getCount('contributor', $this_id, FALSE, $this_type_new) ) $error[] = "article (magazine)";
	if (getCount('article_reference', $this_id, TRUE, 'person') ) $error[] = "article reference";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The person still has the following references: " . implode(", ",$error);
		rexit($this_type, ['person' => $person] );
	} else {
		$name = getone("SELECT CONCAT(firstname, ' ', surname) AS name FROM person WHERE id = $person");

		$q = "DELETE FROM person WHERE id = $person";
		$r = doquery($q);

		if ($r) {
			chlog($person,$this_type,"Person deleted: $name");
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
	$rid = getone("SELECT id FROM person WHERE firstname = '".dbesc($firstname)."' AND surname = '".dbesc($surname)."'");
	if ($rid) {
		$_SESSION['admin']['info'] = "A person with this name <a href=\"person.php?person=$rid\">already exists</a>";
	} elseif (!$firstname) {
		$_SESSION['admin']['info'] = "You are missing a name!";
	} else {
		$q = "INSERT INTO person (id, firstname, surname, internal, birth, death) " .
		     "VALUES (NULL, '".dbesc($firstname)."', '".dbesc($surname)."', '".dbesc($internal)."', " . sqlifnull($birth) . ", " . sqlifnull($death) .")";
		$r = doquery($q);
		if ($r) {
			$person = dbid();
			chlog($person,$this_type,"Person created");
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
print "<tr valign=\"top\"><td>Internal note</td><td><textarea name=\"internal\" cols=50 rows=8 wrap=\"virtual\">\n" . stripslashes(htmlspecialchars($internal)) . "</textarea></td></tr>\n";
tr("Date of birth", "birth", $birth, "", "YYYY-MM-DD", (invaliddate($birth) ? "text" : "date") );
tr("Date of death","death", $death, "", "YYYY-MM-DD", (invaliddate($death) ? "text" : "date") );

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($person ? "Update" : "Create").' person">' . ($person ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete person?\n\nAs a safety mecanism it will be checked if all references are removed.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($person) {
	print changelinks($person,$this_type);
	print changetrivia($person,$this_type);
	print changealias($person,$this_type);
	print showpicture($person,$this_type);
	print showtickets($person,$this_type);

	$q = getall("SELECT g.id, g.title AS title, title.title AS auttitle FROM game g, pgrel, title WHERE g.id = pgrel.game_id AND pgrel.title_id = title.id AND pgrel.person_id = '$person' ORDER BY title.id, g.title");
	print "<tr valign=top><td>Games</td><td>\n";
        foreach($q AS list($id, $title, $auttitle) ) {
		print "<a href=\"game.php?game=$id\">" . htmlspecialchars($title) . "</a> ($auttitle)<br>";
	}
	print "</td></tr>\n";
	$q = getall("SELECT c.id, c.name, c.year, pcrel.role FROM pcrel INNER JOIN convention c ON pcrel.convention_id = c.id WHERE pcrel.person_id = '$person' ORDER BY c.year, c.begin, c.end, c.id");
	print "<tr valign=top><td>Organizer</td><td>\n";
        foreach($q AS list($id, $name, $year, $role) ) {
		print "<a href=\"c.php?con=$id\">" . htmlspecialchars("$name ($year)") . "</a> (" . htmlspecialchars($role) . ")<br>";
	}
	print "</td></tr>\n";
	$q = getall("SELECT COUNT(*), issue.id, issue.title, magazine.name FROM contributor INNER JOIN article ON contributor.article_id = article.id INNER JOIN issue ON article.issue_id = issue.id INNER JOIN magazine ON issue.magazine_id = magazine.id WHERE contributor.person_id = '$person' GROUP BY issue.id, magazine.id, issue.title, magazine.name ORDER BY issue.releasedate, issue.id");
	print "<tr valign=top><td>Contributor<br>(magazine)</td><td>\n";
        foreach($q AS list($count, $issue_id, $title, $name) ) {
		print "<a href=\"magazine.php?issue_id=$issue_id\">" . htmlspecialchars("$name, $title") . "</a> ($count)<br>";
	}
	print "</td></tr>\n";
}
?>
</table>
</form>

</body>
</html>
