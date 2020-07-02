<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$this_type = 'awards';

$category = (string) $_REQUEST['category'];
$action = (string) $_REQUEST['action'];
$do = (string) $_REQUEST['do'];
$name = (string) $_REQUEST['name'];
$description = (string) $_REQUEST['description'];
$nominationtext = (string) $_REQUEST['nominationtext'];
$id = (int) $_REQUEST['id'];
$sce_id = (int) $_REQUEST['sce_id'];
if (!$sce_id) {
	$sce_id = NULL;
}
$award_nominee_entity = (int) $_REQUEST['award_nominee_entity'];
$award_nominee_entity_extra = $_REQUEST['award_nominee_entity'];
$data_id = (int) $_REQUEST['data_id'];
$convent_id = (int) $_REQUEST['convent_id'];
$winner = (int) isset($_REQUEST['winner']);
$ranking = (string) $_REQUEST['ranking'];

$user_id = $_SESSION['user_id'];


$people = [];

$result = getall("SELECT id, firstname, surname FROM aut ORDER BY firstname, surname");
foreach($result AS $row) {
	$people[] = $row['id'] . " - " . $row['firstname'] . " " . $row['surname'];
}

$scenarios = [];
$result = getall("SELECT id, title FROM sce ORDER BY title");
foreach($result AS $row) {
	$scenarios[] = $row['id'] . " - " . $row['title'];
}

// Ret kategori
if ($action == "changecategory" && $do != "Delete") {

	$q = "UPDATE award_categories SET " .
	     "name= '" . dbesc($name) . "', " .
	     "description = '" . dbesc($description) . "' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Pris rettet: $id, $name");
	}
	$_SESSION['admin']['info'] = "Award updated! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Slet kategori
if ($action == "changecategory" && $do == "Delete") {
	// Tjek at der ikke er nogen nominerede, før der kan slettes
	$q = "SELECT COUNT(*) FROM award_nominees where award_category_id = $id";
	$r = getone($q);
	if($r != 0) {
		$_SESSION['admin']['info'] = "The category needs to be empty before it can be removed! " . dberror();
		rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
	}

	$q = "DELETE FROM award_categories WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Kategori fjernet: $id");
	}
	$_SESSION['admin']['info'] = "Category removed! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Tilføj kategori
if ($action == "addcategory") {
	$q = "INSERT INTO award_categories " .
	     "(name, description, convent_id) VALUES ".
	     "('" . dbesc($name) . "', '" . dbesc($description) . "', " . $data_id .")";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id,$category,"Pris oprettet: $name");
	}
	$_SESSION['admin']['info'] = "Award created! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Ret nomineret
if ($action == "changenominee" && $do != "Delete") {
	$award_name = getone("SELECT name FROM award_categories WHERE id = $data_id");
	$q = "UPDATE award_nominees SET " .
	     "name= '" . dbesc($name) . "', " .
	     "sce_id = " . strNullEscape($sce_id) . ", " . 
	     "nominationtext = '" . dbesc($nominationtext) . "', " .
	     "winner = $winner, " .
	     "ranking = '" . dbesc($ranking) . "' " .
	     "WHERE id = '$id'";
	if ($award_nominee_entity) { // assuming person
	     doquery("INSERT INTO award_nominee_entities (award_nominee_id, data_id, category) VALUES ($id, $award_nominee_entity, 'aut')");
	} elseif ($award_nominee_entity_extra) { // Label
		doquery("INSERT INTO award_nominee_entities (award_nominee_id, label) VALUES ($id, '" . dbesc($award_nominee_entity_extra) . "')");
	}
	$r = doquery($q);
	if ($r) {
		chlog($convent_id,'convent',"Nomineret rettet: $name ($data_id), $award_name");
	}
	$_SESSION['admin']['info'] = "Nominee updated! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Tilføj nomineret
if ($action == "addnominee") {
	var_dump($convent_id);
	$award_name = getone("SELECT name FROM award_categories WHERE id = $data_id");
	$q = "INSERT INTO award_nominees " .
	     "(award_category_id, sce_id, name, nominationtext, winner, ranking) VALUES ".
	     "($data_id, " . strNullEscape($sce_id) . ", '" . dbesc($name) . "', '" . dbesc($nominationtext) . "', " . $winner .", '" . dbesc($ranking) ."')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($convent_id,'convent',"Nomineret oprettet: $name, $award_name");
	}
	$_SESSION['admin']['info'] = "Nominee added! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

// Slet nomineret
if ($action == "changenominee" && $do == "Delete") {
	// Tjek at der ikke er nogen nominerede, før der kan slettes
	$num_childs = getone("SELECT COUNT(*) FROM award_nominee_entities where award_nominee_id = $id");
	if($num_childs != 0) {
		$_SESSION['admin']['info'] = "The nominee must have all entities removed, before it can be deleted! " . dberror();
		rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
	}

	$q = "DELETE FROM award_nominees WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($convent_id,'convent',"Nomineret fjernet: $id");
	}
	$_SESSION['admin']['info'] = "Nominee removed! " . dberror();
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}

if ($action == 'deletenomineeentity') {
	if (getone("SELECT id FROM award_nominee_entities WHERE id = $id") ) {
		doquery("DELETE FROM award_nominee_entities WHERE id = $id");
		chlog($convent_id,'convent',"Nominerings-tilknytning slettet: $id");
		$_SESSION['admin']['info'] = "Connection removed! " . dberror();
	} else {
		$_SESSION['admin']['info'] = "Could not find connection! " . dberror();
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id ] );
}


?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Awards</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type="text/javascript">
	$(function() {
		var availablePeople = <?php print json_encode($people); ?>;
		$( ".peopletags" ).autocomplete({
			source: availablePeople,
			autoFocus: true,
			delay: 10
		});
		var availableScenarios= <?php print json_encode($scenarios); ?>;
		$( ".scenariotags" ).autocomplete({
			source: availableScenarios,
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

<body>
<?php
include("links.inc");

printinfo();

if ($category == 'convent') {
	// get data
	$cat = 'convent';
	$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
	$mainlink = "convent.php?con=$data_id";

	$title = getone($q);
	
	$query = "SELECT a.id, a.name, a.description, SUM(b.winner = 1) AS winners, COUNT(b.id) AS nominees FROM award_categories a LEFT JOIN award_nominees b ON a.id = b.award_category_id WHERE convent_id = '$data_id' GROUP BY a.id";
	$result = getall($query);

	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Edit awards for: <a href=\"$mainlink\" accesskey=\"q\">" . htmlspecialchars($title) . "</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Award</th>".
	      "<th>Description</th>".
	      "</tr>\n";

	if ($result) {
	        foreach($result AS $row) {
			print '<form action="awards.php" method="post">'.
			      '<input type="hidden" name="action" value="changecategory">'.
			      '<input type="hidden" name="category" value="'.$category.'">'.
			      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
			      '<input type="hidden" name="id" value="'.$row['id'].'">';
			print "<tr valign=\"top\">\n".
			      '<td style="text-align:right;">'.$row['id'].'</td>'.
			      '<td><input type="text" name="name" value="'.htmlspecialchars($row['name']).'" size=40 maxlength=150><br><a href="awards.php?category=awardcategory&amp;data_id=' . $row['id'] . '">' . ($row['winners'] == 1 ? "1 vinder" : (int) $row['winners'] . " vindere") . " / " . ($row['nominees'] == 1 ? "1 nomineret" : $row['nominees'] . " nominerede") . '</a></td>'.
			      '<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;">'.htmlspecialchars($row['description']).'</textarea></td>'.
			      '<td><input type="submit" name="do" value="Update"> '.
			      ($row['nominees'] == 0 ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove award?\');">' : '') . '</td>'.
			      "</tr>\n";
			print "</form>\n\n";
		}

	}
	print '<form action="awards.php" method="post">'.
	      '<input type="hidden" name="action" value="addcategory">'.
	      '<input type="hidden" name="category" value="'.$category.'">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">';
	print "<tr valign=\"top\">\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="name" value="" size=40 maxlength=150 autofocus></td>'.
	      '<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;"></textarea></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      "</tr>\n";
	print "</form>\n\n";


} elseif ($category == "awardcategory" && $data_id) {
	// get category
	list($category_id, $name, $convent_id, $convent_name, $year) = getrow("SELECT a.id, a.name, a.convent_id, b.name AS convent_name, b.year FROM award_categories a LEFT JOIN convent b ON a.convent_id = b.id WHERE a.id = $data_id");
	if (!$category_id) {
		die("Unknown award category");
	}
	$nominees = getall("SELECT a.id, a.sce_id, a.name, a.nominationtext, a.winner, a.ranking, b.title, COUNT(c.id) AS count_entity FROM award_nominees a LEFT JOIN sce b ON a.sce_id = b.id LEFT JOIN award_nominee_entities c ON a.id = c.award_nominee_id WHERE award_category_id = $data_id GROUP BY a.id ORDER BY winner DESC, a.id");

	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Edit nominees for " . htmlspecialchars($name) . " at: <a href=\"awards.php?category=convent&amp;data_id=$convent_id\" accesskey=\"q\">" . htmlspecialchars($convent_name) . " ($year)</a>".
	      "</th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Nominee</th>".
	      "<th>Scenario (optional)</th>".
	      "<th>Nominee text</th>".
	      "<th>Winner?</th>".
	      "<th>Position (optional)</th>".
	      "<th></th>" . 
	      "</tr>\n";
	foreach($nominees AS $nominee) {
		$sce_id = "";
		if ($nominee['title']) {
			$sce_id = $nominee['sce_id'] . " - " . $nominee['title'];
		}
		$html_entity = "";
		$html_entity .= '<div style="margin-left: 3em;">';
		$html_entity .= ($nominee['count_entity'] == 1 ? '1 connection' : $nominee['count_entity'] . " connections");
		$html_entity .= ' <a href="#" onclick="this.nextSibling.style.display=\'block\'; this.nextSibling.focus(); this.style.display=\'none\'; return false;">[+]</a>';
		$html_entity .= '<input name="award_nominee_entity" style="font-size: 0.7em; display: none;" class="peopletags" placeholder="Name of individual nominee">';
		$entities = getall("SELECT id, data_id,category, label FROM award_nominee_entities WHERE award_nominee_id = " . $nominee['id'] . " ORDER BY id");
		$html_entity .= '<br>';
		foreach($entities AS $entity) {
			$html_entity .= '<a href="#" onclick="if (confirm(\'Do you want to delete this connection?\') ) { location.href=\'awards.php?category=awardcategory&amp;data_id=' . $data_id . '&amp;convent_id=' . $convent_id . '&amp;action=deletenomineeentity&amp;id=' . $entity['id'] . '\'; } else { return false; }">[delete]</a> ';
			if ($entity['category']) {
				$name = getentry($entity['category'], $entity['data_id']);	
				$link = getdatalink($entity['category'], $entity['data_id'], TRUE);
				$linkhtml = getdatahtml($entity['category'], $entity['data_id'], $name, TRUE);
				$html_entity .= $linkhtml;
			} else {
				$html_entity .= $entity['label'];
			}
			$html_entity .= "<br>" . PHP_EOL;
		}
		$html_entity .= '</div>';

		print '<form action="awards.php" method="post">'.
		      '<input type="hidden" name="action" value="changenominee">'.
		      '<input type="hidden" name="category" value="'.$category.'">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="convent_id" value="'.$convent_id.'">'.
		      '<input type="hidden" name="id" value="'.$nominee['id'].'">';
		print "<tr valign=\"top\">\n".
		      '<td style="text-align:right;">'.$nominee['id'].'</td>'.
		      '<td><input type="text" name="name" value="'.htmlspecialchars($nominee['name']).'" size=40 maxlength=150 placeholder="(leave blank for scenario or board game)"><br>' .
		      $html_entity . 
		      '</td>'.
		      '<td><input type="text" name="sce_id" value="'.htmlspecialchars($sce_id).'" size=30 maxlength=150 class="scenariotags"></td>'.
		      '<td><textarea name="nominationtext" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;" >'.htmlspecialchars($nominee['nominationtext']).'</textarea></td>'.
		      '<td style="text-align: center;"><input type="checkbox" name="winner" value="yes" ' . ($nominee['winner'] ? 'checked' : '' ) . '></td>'.
		      '<td><input type="text" name="ranking" value="'.htmlspecialchars($nominee['ranking']).'" size=10 maxlength=150><br>' .
		      '<td><input type="submit" name="do" value="Update"> '.
		      ($nominee['count_entity'] == 0 ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove nominee?\');">' : '') . '</td>'.
		      "</tr>\n";
		print "</form>\n\n";
		
	}
	print '<form action="awards.php" method="post">'.
	      '<input type="hidden" name="action" value="addnominee">'.
	      '<input type="hidden" name="category" value="'.$category.'">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="convent_id" value="'.$convent_id.'">';
	print "<tr valign=\"top\">\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="name" value="" size=40 maxlength=150 placeholder="(leave blank for scenario or board game)" autofocus></td>'.
	      '<td><input type="text" name="sce_id" value="" size=30 maxlength=150 class="scenariotags"></td>'.
	      '<td><textarea name="nominationtext" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;" ></textarea></td>'.
	      '<td style="text-align: center;"><input type="checkbox" name="winner" value="yes" ' . (count($nominees) == 0 ? 'checked' : '' ) . '></td>'.
	      '<td><input type="text" name="ranking" value="" size=10 maxlength=150></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      "</tr>\n";
	print "</form>\n\n";
	
	print "</table>" . PHP_EOL;
	
}

print "</body>\n</html>\n";

?>
