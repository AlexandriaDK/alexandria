<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'run';

$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$begin = $_REQUEST['begin'];
$end = $_REQUEST['end'];
$location = (string) $_REQUEST['location'];
$description = (string) $_REQUEST['description'];
$id = (int) $_REQUEST['id'];
$run_id = (int) $_REQUEST['run_id'];
$cancelled = (int) isset($_REQUEST['cancelled']);

$q = "SELECT title FROM sce WHERE id = '$id'";
$title = getone($q);

// Ret afvikling
if ($action == "changerun" && $do != "Slet") {
	$begin = trim($begin);
	$end = trim($end);
	if (strlen($begin) == 4) $begin .= "-00-00"; // add blank month+date
	if (strlen($begin) == 7) $begin .= "-00"; // add blank date
	if (!$end) $end = $begin;
	$location = trim($location);
	$q = "UPDATE scerun SET " .
	     "begin = '$begin', " .
	     "end = '$end', " .
	     "location = '" . dbesc($location) . "', " .
	     "description = '" . dbesc($description) . "', " .
	     "cancelled = $cancelled " .
	     "WHERE id = '$run_id'";
	$r = doquery($q);
	if ($r) {
		chlog($id,'sce',"Afvikling rettet");
	}
	$_SESSION['admin']['info'] = "Afvikling rettet! " . dberror();
	rexit( $this_type, [ 'id' => $id ] );
}

// Slet afvikling
if ($action == "changerun" && $do == "Slet") {
	$q = "DELETE FROM scerun WHERE id = '$run_id'";
	$r = doquery($q);
	if ($r) {
		chlog($id,'sce',"Afvikling slettet");
	}
	$_SESSION['admin']['info'] = "Afvikling slettet! " . dberror();
	rexit( $this_type, [ 'id' => $id ] );
}

// Tilføj afvikling
if ($action == "addrun") {
	$begin = trim($begin);
	$end = trim($end);
	if (strlen($begin) == 4) $begin .= "-00-00"; // add blank month+date
	if (strlen($begin) == 7) $begin .= "-00"; // add blank date
	if (!$end) $end = $begin;
	$q = "INSERT INTO scerun " .
	     "(sce_id, begin, end, location, description, cancelled) VALUES ".
	     "('$id', '$begin', '$end', '" . dbesc($location). "', '" . dbesc($description) . "', $cancelled)";
	$r = doquery($q);
	if ($r) {
		chlog($id,'sce',"Afvikling oprettet");
	}
	$_SESSION['admin']['info'] = "Afvikling oprettet! " . dberror();
	rexit( $this_type, [ 'id' => $id ] );
}

$query = "SELECT id, begin, end, location, description, cancelled FROM scerun WHERE sce_id = '$id' ORDER BY begin, end, id";
$result = getall($query);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - afvikling</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

printinfo();

if ($id) {

	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=6>Ret afvikling for: <a href=\"scenarie.php?scenarie=$id\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Startdato</th>".
	      "<th>Slutdato</th>".
	      "<th>Lokalitet</th>".
	      "<th>Evt. note</th>".
	      "<th>Afvikling aflyst?</th>".
	      "</tr>\n";

	foreach($result AS $row) {
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
		      '<input type="hidden" name="action" value="changerun">'.
		      '<input type="hidden" name="id" value="'.$id.'">'.
		      '<input type="hidden" name="run_id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="date" name="begin" value="'.htmlspecialchars($row['begin']).'" size="12" maxlength="20" placeholder="ÅÅÅÅ-MM-DD"></td>'.
		      '<td><input type="date" name="end" value="'.htmlspecialchars($row['end']).'" size="12" maxlength="20" placeholder="ÅÅÅÅ-MM-DD"></td>'.
		      '<td><input type="text" name="location" value="'.htmlspecialchars($row['location']).'" size="30" maxlength="80"></td>'.
		      '<td><input type="text" name="description" value="'.htmlspecialchars($row['description']).'" size="30" ></td>'.
		      '<td align="center"><input type="checkbox" name="cancelled" value="yes" ' . ($row['cancelled'] ? 'checked' : '' ) . '></td>'.
		      '<td><input type="submit" name="do" value="Ret"></td>'.
		      '<td><input type="submit" name="do" value="Slet"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
	      '<input type="hidden" name="action" value="addrun">'.
	      '<input type="hidden" name="id" value="'.$id.'">';
	print "<tr>\n".
	      '<td style="text-align:right;">Ny</td>'.
	      '<td><input type="date" name="begin" value="" size="12" maxlength="20" placeholder="ÅÅÅÅ-MM-DD"></td>'.
	      '<td><input type="date" name="end" value="" size="12" maxlength="20" placeholder="ÅÅÅÅ-MM-DD"></td>'.
	      '<td><input type="text" name="location" value="" size="30" maxlength="80"></td>'.
	      '<td><input type="text" name="description" value="" size="30" ></td>'.
	      '<td align="center"><input type="checkbox" name="cancelled" value="yes"></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Opret"></td>'.
	      "</tr>\n";
	print "</form>\n\n";


	print "</table>\n";
} else {
	print "Fejl: Intet data-id angivet.";
}
print "</body>\n</html>\n";

?>
