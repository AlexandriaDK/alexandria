<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'run';

$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$begin = trim( $_REQUEST['begin'] );
$end = trim( $_REQUEST['end'] );
$location = trim( (string) $_REQUEST['location'] );
$country = trim( (string) $_REQUEST['country'] );
$description = (string) $_REQUEST['description'];
$id = (int) $_REQUEST['id'];
$run_id = (int) $_REQUEST['run_id'];
$cancelled = (int) isset($_REQUEST['cancelled']);

$q = "SELECT title FROM sce WHERE id = '$id'";
$title = getone($q);

function typechange($type) {
	if ($type !== 'date') {
		return '';
	}
	$html = ' <span onclick="switchType($(this).prev());" class="changeTypeCog">⚙️</span>';
	return $html;
}

// Update run
if ($action == "changerun" && $do != "Delete") {
	if (strlen($begin) == 4) $begin .= "-00-00"; // add blank month+date
	if (strlen($begin) == 7) $begin .= "-00"; // add blank date
	if (strlen($end) == 4) $end .= "-00-00"; // add blank month+date
	if (strlen($end) == 7) $end .= "-00"; // add blank date
	if (!$end) $end = $begin;
	$q = "UPDATE scerun SET " .
	     "begin = '$begin', " .
	     "end = '$end', " .
	     "location = '" . dbesc($location) . "', " .
	     "country = '" . dbesc($country) . "', " .
	     "description = '" . dbesc($description) . "', " .
	     "cancelled = $cancelled " .
	     "WHERE id = '$run_id'";
	$r = doquery($q);
	if ($r) {
		chlog($id,'sce',"Run updated");
	}
	$_SESSION['admin']['info'] = "Run updated! " . dberror();
	rexit( $this_type, [ 'id' => $id ] );
}

// Delete run
if ($action == "changerun" && $do == "Delete") {
	$q = "DELETE FROM scerun WHERE id = '$run_id'";
	$r = doquery($q);
	if ($r) {
		chlog($id,'sce',"Run deleted");
	}
	$_SESSION['admin']['info'] = "Run deleted! " . dberror();
	rexit( $this_type, [ 'id' => $id ] );
}

// Tilføj afvikling
if ($action == "addrun") {
	if (strlen($begin) == 4) $begin .= "-00-00"; // add blank month+date
	if (strlen($begin) == 7) $begin .= "-00"; // add blank date
	if (!$end) $end = $begin;
	$q = "INSERT INTO scerun " .
	     "(sce_id, begin, end, location, country, description, cancelled) VALUES ".
	     "('$id', '$begin', '$end', '" . dbesc($location). "', '" . dbesc($country). "', '" . dbesc($description) . "', $cancelled)";
	$r = doquery($q);
	if ($r) {
		chlog($id,'sce',"Run created");
	}
	$_SESSION['admin']['info'] ="Run created! " . dberror();
	rexit( $this_type, [ 'id' => $id ] );
}

$query = "SELECT id, begin, end, location, country, description, cancelled FROM scerun WHERE sce_id = '$id' ORDER BY begin, end, id";
$result = getall($query);

htmladmstart("Run");

if ($id) {

	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=6>Edit runs for: <a href=\"game.php?game=$id\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Start date</th>".
	      "<th>End date</th>".
	      "<th>Location</th>".
	      "<th title=\"Two letter ISO code\">Country</th>".
	      "<th>Note</th>".
	      "<th>Run cancelled?</th>".
	      "</tr>\n";

	foreach($result AS $row) {
		$typebegin = (preg_match( '/-00$/', $row['begin'] ) ? 'text' : 'date' );
		$typeend = (preg_match( '/-00$/', $row['end'] ) ? 'text' : 'date' );
		print '<form action="run.php" method="post">'.
		      '<input type="hidden" name="action" value="changerun">'.
		      '<input type="hidden" name="id" value="'.$id.'">'.
		      '<input type="hidden" name="run_id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="' . $typebegin . '" name="begin" value="'.htmlspecialchars($row['begin']).'" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange($typebegin) . '</td>'.
		      '<td><input type="' . $typeend . '" name="end" value="'.htmlspecialchars($row['end']).'" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange($typeend) . '</td>'.
			  '<td><input type="text" name="location" value="'.htmlspecialchars($row['location']).'" size="30" maxlength="80"></td>'.
			  '<td><input type="text" id="country" name="country" value="' . htmlspecialchars( $row['country'] ) . '" placeholder="E.g. se" size="8"></td>'.
		      '<td><input type="text" name="description" value="'.htmlspecialchars($row['description']).'" size="30" ></td>'.
		      '<td align="center"><input type="checkbox" name="cancelled" value="yes" ' . ($row['cancelled'] ? 'checked' : '' ) . '></td>'.
		      '<td><input type="submit" name="do" value="Update"></td>'.
		      '<td><input type="submit" name="do" value="Delete"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="run.php" method="post">'.
	      '<input type="hidden" name="action" value="addrun">'.
	      '<input type="hidden" name="id" value="'.$id.'">';
	print "<tr>\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="date" name="begin" value="" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange('date') . '</td>'.
	      '<td><input type="date" name="end" value="" size="12" maxlength="20" placeholder="YYYY-MM-DD">' . typechange('date') . '</td>'.
	      '<td><input type="text" name="location" value="" size="30" maxlength="80"></td>'.
		  '<td><input type="text" id="country" name="country" value="" placeholder="E.g. se" size="8"></td>'.
	      '<td><input type="text" name="description" value="" size="30" ></td>'.
	      '<td align="center"><input type="checkbox" name="cancelled" value="yes"></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      "</tr>\n";
	print "</form>\n\n";


	print "</table>\n";
} else {
	print "Error: No data id provided.";
}
print "</body>\n</html>\n";

?>
