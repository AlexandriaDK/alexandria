<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'links';

$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$url = $_REQUEST['url'];
$description = $_REQUEST['description'];
$id = $_REQUEST['id'];
$category = $_REQUEST['category'];
$data_id = $_REQUEST['data_id'];
if ($category == 'game') $category = 'sce';

$url = trim($url);
if ($url && substr($url,0,4) != 'http' && substr($url,0,1) != '{') {
	$url = 'https://' . $url;
}

function rlyehlink ($text) {
	if (preg_match('/^r(lyeh)?(\d+)/i',$text,$regs)) {
		return $regs[2];
	} else {
		return false;
	}
}

if ( $action ) {
	validatetoken( $token );
}

// Ret link
if ($action == "changelink" && $do != "Remove") {
	$url = trim($url);
	$description = trim($description);
	if ($rid = rlyehlink($url)) {
		$url = "http://rlyeh.alexandria.dk/pub/scenarier/game.php3?id=".$rid;
		$description = "Scenariet til download på Projekt R\\'lyeh";
	}
	$q = "UPDATE links SET " .
	     "url = '" . dbesc($url) . "', " .
	     "description = '" . dbesc($description) . "' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Link rettet");
	}
	$_SESSION['admin']['info'] = "Link updated! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Remove link
if ($action == "changelink" && $do == "Remove") {
	$q = "DELETE FROM links WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Link slettet");
	}
	$_SESSION['admin']['info'] = "Link removed! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Add link
if ($action == "addlink") {
	$url = trim($url);
	$description = trim($description);
	if ($rid = rlyehlink($url)) {
		$url = "http://rlyeh.alexandria.dk/pub/scenarier/game.php3?id=".$rid;
		$description = "Scenariet til download på Projekt R\\'lyeh";
	}
	$q = "INSERT INTO links " .
	     "(data_id, category, url, description) VALUES ".
	     "('$data_id', '$category', '" . dbesc($url) . "', '" . dbesc($description) . "')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id,$category,"Link oprettet");
	}
	$_SESSION['admin']['info'] = "Link added! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );

}


if ($data_id && $category) {
	$data_id = intval($data_id);
	switch($category) {
	case 'aut':
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
		break;
	case 'sce':
		$cat = 'sce';
		$q = "SELECT title FROM sce WHERE id = '$data_id'";
		$mainlink = "game.php?game=$data_id";
		break;
	case 'convent':
		$cat = 'convent';
		$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
		$mainlink = "convent.php?con=$data_id";
		break;
	case 'conset':
		$cat = 'conset';
		$q = "SELECT name FROM conset WHERE id = '$data_id'";
		$mainlink = "conset.php?conset=$data_id";
		break;
	case 'sys':
		$cat = 'sys';
		$q = "SELECT name FROM sys WHERE id = '$data_id'";
		$mainlink = "system.php?system=$data_id";
		break;
	case 'tag':
		$cat = 'tag';
		$q = "SELECT tag FROM tag WHERE id = '$data_id'";
		$mainlink = "tag.php?tag_id=$data_id";
		break;
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, url, description FROM links WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id";
	$result = getall($query);
}

htmladmstart("Links");

if ($data_id && $category) {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Edit links for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>URL</th>".
	      "<th>Description</th>".
	      "</tr>\n";

	foreach($result AS $row) {
		print '<form action="links.php" method="post">'.
			  '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
			  '<input type="hidden" name="action" value="changelink">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="text" name="url" value="'.htmlspecialchars($row['url']).'" size=40 maxlength=200></td>'.
		      '<td><input type="text" name="description" value="'.htmlspecialchars($row['description']).'" size=40 maxlength=200></td>'.
		      '<td><input type="submit" name="do" value="Update"></td>'.
		      '<td><input type="submit" name="do" value="Remove"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="links.php" method="post">'.
	      '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
	      '<input type="hidden" name="action" value="addlink">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">';
	print "<tr>\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="url" value="" size=40 maxlength=200></td>'.
	      '<td><input type="text" name="description" id="newdescription" value="" size=40 maxlength=200></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Add"></td>'.
	      "</tr>\n";
	print "</form>\n\n";

	$descriptions = [
		'{$_links_website}' => 'Website',
		'{$_links_website_scenario}' => 'Scenario website',
		'{$_links_website_con}' => 'Con website',
		'{$_links_programme}' => 'Programme',
		'{$_links_facebook_page}' => 'Facebook page',
		'{$_links_facebook_event}' => 'Facebook event',
		'{$_links_facebook_event_scenario}' => 'Facebook event for scenario',
		'{$_links_facebook_event_con}' => 'Facebook event for con',
		'{$_links_rules}' => 'Rules',
		'{$_links_bgg}' => 'BoardGameGeek entry',
		'{$_links_description}' => 'Description',
	];
	print '<tr><td></td><td></td><td colspan="3">';
	foreach( $descriptions AS $templatecode => $label ) {
		print '<div class="descriptionexamples">';
		print "<a href=\"#\" onclick=\"document.getElementById('newdescription').value=this.dataset.smartycode;\" data-smartycode=\"" . htmlspecialchars( $templatecode ) . "\" title=\"Add to new description\">";
		print htmlspecialchars( $label );
		print '</a> <span onclick="navigator.clipboard.writeText(this.innerHTML); $(this).fadeOut(100).fadeIn(100);">' . htmlspecialchars( $templatecode ) . '</span>';
		print '</div>';
	}

	print "</td></tr>\n";

	print "</table>\n";
} else {
	print "Error: No data id provided.";
}

print "</body>\n</html>\n";

?>
