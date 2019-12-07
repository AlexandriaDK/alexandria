<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$this_type = 'organizers';
$category = 'convent';

$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$role = (string) $_REQUEST['role'];
$aut_text = (string) $_REQUEST['aut_text'];
$aut_id = (int) $aut_text;
$aut_extra = "";
if (!$aut_id) {
	$aut_extra = $aut_text;
	$aut_id = NULL;
}

$id = $_REQUEST['id'];
$data_id = (int) $_REQUEST['data_id'];

$user_id = $_SESSION['user_id'];

$people = [];
$r = getall("SELECT id, firstname, surname FROM aut ORDER BY firstname, surname");
foreach($r AS $row) {
	$people[] = $row['id'] . " - " . $row['firstname'] . " " . $row['surname'];
}

// Ret arrangør
if ($action == "changeorganizer" && $do != "Slet") {

	$q = "UPDATE acrel SET " .
	     "aut_id = " . strNullEscape($aut_id) . ", " .
	     "aut_extra = '" . dbesc($aut_extra) . "', " .
	     "role = '" . dbesc($role) . "', " .
	     "added_by_user_id = $user_id " . 
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		if ((int) $aut_id) {
			chlog($data_id,$category,"Arrangør rettet: $aut_id");
		} else {
			chlog($data_id,$category,"Arrangør rettet: $aut_extra");
		}
	}
	$_SESSION['admin']['info'] = "Arrangør rettet! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Slet arrangør
if ($action == "changeorganizer" && $do == "Slet") {
	$q = "DELETE FROM acrel WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Arrangør fjernet");
	}
	$_SESSION['admin']['info'] = "Arrangør fjernet! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Tilføj arrangør
if ($action == "addorganizer") {
	$q = "INSERT INTO acrel " .
	     "(aut_id, aut_extra, convent_id, role, added_by_user_id) VALUES ".
	     "(" . strNullEscape($aut_id) . ", '" . dbesc($aut_extra) . "',  $data_id, '" . dbesc($role) . "', " . $_SESSION['user_id'] .")";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		if ((int) $aut_id) {
			chlog($data_id,$category,"Arrangør oprettet: $aut_id");
		} else {
			chlog($data_id,$category,"Arrangør oprettet: $aut_extra");
		}
	}
	$_SESSION['admin']['info'] = "Arrangør oprettet! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}


if ($data_id) {
	$cat = 'convent';
	$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
	$mainlink = "convent.php?con=$data_id";

	$title = getone($q);
	
#	$query = "SELECT id, aut_id, aut_extra, role FROM acrel WHERE convent_id = '$data_id' ORDER BY id";
	$query = "SELECT a.id, a.aut_id, a.aut_extra, CONCAT(b.firstname, ' ', b.surname) AS fullname, a.role FROM acrel a LEFT JOIN aut b ON a.aut_id = b.id WHERE convent_id = $data_id ORDER BY id";
	$result = getall($query);

}

?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - arrangører</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type="text/javascript">
	$(function() {
		var availableNames = <?php print json_encode($people); ?>;
		$( ".tags" ).autocomplete({
			source: availableNames,
			autoFocus: true,
			delay: 10
		});
	});
	</script>
	<style type="text/css">
		.ui-autocomplete {
			max-height: 300px;
			overflow-y: auto;
			/* prevent horizontal scrollbar */
			    overflow-x: hidden;
			font-size: 0.7em;
		  }
	</style>


</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

printinfo();

print "<table align=\"center\" border=0>".
      "<tr><th colspan=5>Ret arrangører for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
      "<tr>\n".
      "<th>ID</th>".
      "<th>Rolle</th>".
      "<th>Person</th>".
      "</tr>\n";

if ($result) {
	foreach($result AS $row) {
		$aut_text = "";
		if ($row['aut_id']) $aut_text .= $row['aut_id'] . " - ";
		if ($row['fullname']) $aut_text .= $row['fullname'];
		if ($row['aut_extra']) $aut_text .= $row['aut_extra'];
		print '<form action="organizers.php" method="post">'.
		      '<input type="hidden" name="action" value="changeorganizer">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="text" name="role" value="'.htmlspecialchars($row['role']).'" size=40 maxlength=100></td>'.
		      '<td><input type="text" name="aut_text" value="'.htmlspecialchars($aut_text).'" size=40 maxlength=100 class="tags"></td>'.
		      '<td><input type="submit" name="do" value="Ret"></td>'.
		      '<td><input type="submit" name="do" value="Slet"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}
}

print '<form action="organizers.php" method="post">'.
      '<input type="hidden" name="action" value="addorganizer">'.
      '<input type="hidden" name="data_id" value="'.$data_id.'">';
print "<tr>\n".
      '<td style="text-align:right;">Ny</td>'.
      '<td><input type="text" name="role" value="" size=40 maxlength=100 autofocus></td>'.
      '<td><input type="text" name="aut_text" value="" size=40 maxlength=100 class="tags"></td>'.
      '<td colspan=2><input type="submit" name="do" value="Opret"></td>'.
      "</tr>\n";
print "</form>\n\n";

print "</table>\n";
print "</body>\n</html>\n";

?>
