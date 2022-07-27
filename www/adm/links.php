<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'links';

$action = $_REQUEST['action'] ?? '';
$do = $_REQUEST['do'] ?? '';
$url = $_REQUEST['url'] ?? '';
$description = $_REQUEST['description'] ?? '';
$id = $_REQUEST['id'] ?? '';
$category = $_REQUEST['category'] ?? '';
$data_id = $_REQUEST['data_id'] ?? '';

$url = trim($url);
if ($url && substr($url,0,4) != 'http' && substr($url,0,1) != '{') {
	$url = 'https://' . $url;
}

if ( $action ) {
	validatetoken( $token );
}

// Ret link
if ($action == "changelink" && $do != "Remove") {
	$url = trim($url);
	$description = trim($description);
	$q = "UPDATE links SET " .
	     "url = '" . dbesc($url) . "', " .
	     "description = '" . dbesc($description) . "' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Link updated");
	}
	$_SESSION['admin']['info'] = "Link updated! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Remove link
if ($action == "changelink" && $do == "Remove") {
	$q = "DELETE FROM links WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Link removed");
	}
	$_SESSION['admin']['info'] = "Link removed! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Add link
if ($action == "addlink") {
	$url = trim($url);
	$description = trim($description);
	$data_field = getFieldFromCategory($category);
	$q = "INSERT INTO links " .
	     "($data_field, url, description) VALUES ".
	     "('$data_id', '" . dbesc($url) . "', '" . dbesc($description) . "')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id,$category,"Link added");
	}
	$_SESSION['admin']['info'] = "Link added! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );

}

if ($data_id && $category) {
	$data_id = intval($data_id);
	$data_field = getFieldFromCategory($category);
	$linktitle = getlabel($category, $data_id, TRUE);
	
	$query = "SELECT id, url, description FROM links WHERE `$data_field` = '$data_id' ORDER BY id";
	$result = getall($query);
}

htmladmstart("Links");

if ($data_id && $category) {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Edit links for: $linktitle</th></tr>\n".
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
		'{$_links_facebook_group}' => 'Facebook group',
		'{$_links_facebook_page}' => 'Facebook page',
		'{$_links_facebook_event}' => 'Facebook event',
		'{$_links_facebook_event_scenario}' => 'Facebook event for scenario',
		'{$_links_facebook_event_con}' => 'Facebook event for con',
		'{$_links_rules}' => 'Rules',
		'{$_links_bgg}' => 'BoardGameGeek entry',
		'{$_links_description}' => 'Description',
		'{$_links_nordiclarpwiki}' => 'Nordic Larp Wiki article',
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
