<?php
$admonly = TRUE;
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'rss';

$action = $_REQUEST['action'];
$url = $_REQUEST['url'];
$pageurl = $_REQUEST['pageurl'];
$aut_id = $_REQUEST['aut_id'];
$id = $_REQUEST['id'];
$owner = $_REQUEST['owner'];
$name = $_REQUEST['name'];
$do = $_REQUEST['do'];

// Ret link
if ($action == "changelink" && $do != "Slet") {
	$url = trim($url);
	$pageurl = trim($pageurl);
	$aut_id = trim($aut_id);
	$owner = trim($owner);
	$name = trim($name);
	$q = "UPDATE feeds SET " .
	     "url = '$url', " .
	     "pageurl = '$pageurl', " .
	     "owner = '$owner', " .
	     "name = '$name', " .
	     "aut_id = '$aut_id' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
//		chlog($id,$this_type,"Link rettet");
	}
	$info = "Link rettet! " . dberror();
}

// Tøm feed
if ($action == "changelink" && $do == "Tøm") {
	$q = "DELETE FROM feedcontent WHERE feed_id = '$id'";
	$r = doquery($q);
	$info = "Indhold slettet! " . dberror();
	if ($r) {
//		chlog($id,$this_type,"Link slettet");
	}
}

// Slet feed
if ($action == "changelink" && $do == "Slet") {
	$q = "DELETE FROM feedcontent WHERE feed_id = '$id'";
	$r = doquery($q);
	$q = "DELETE FROM feeds WHERE id = '$id'";
	$r = doquery($q);
	$info = "Feed slettet! " . dberror();
	if ($r) {
//		chlog($id,$this_type,"Link slettet");
	}
}

// Tilføj link
if ($action == "addlink") {
	$url = trim($url);
	$owner = trim($owner);
	$name = trim($name);
	$pageurl = trim($pageurl);
	$aut_id = trim($aut_id);
	$q = "INSERT INTO feeds " .
	     "(url, owner, name, pageurl, aut_id) VALUES ".
	     "('$url', '$owner', '$name', '$pageurl','$aut_id')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
//		chlog($id,$this_type,"Link oprettet");
	}
	$info = "Link oprettet! " . dberror();
}

	$query = "SELECT a.id, a.url, a.owner, a.name, a.pageurl, a.aut_id, COUNT(b.id) AS count FROM feeds a LEFT JOIN feedcontent b ON a.id = b.feed_id GROUP BY a.id ORDER BY a.id";
	$result = getrow($query);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - Feeds</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

print "<table align=\"center\" border=0>".
      "<tr><th colspan=7>Ret feeds:</th></tr>\n".
      "<tr>\n".
      "<th>ID</th>".
      "<th colspan='2'>URL</th>".
      "<th>Ejer</th>".
      "<th>Navn</th>".
      "<th>Antal</th>".
      "</tr>\n";

if ($result) {
	foreach($result AS $row) {
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
		      '<input type="hidden" name="action" value="changelink">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr valign='top'>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td>Side:<br />Feed:</td>'.
		      '<td><input type="text" name="pageurl" value="'.htmlspecialchars($row['pageurl']).'" size=50 maxlength=100><br /><input type="text" name="url" value="'.htmlspecialchars($row['url']).'" size=50 maxlength=100></td>'.
		      '<td><input type="text" name="owner" value="'.htmlspecialchars($row['owner']).'" size=30 maxlength=100><br /><input type="text" name="aut_id" value="'.htmlspecialchars($row['aut_id']).'" size=3 maxlength=10></td>'.
	      '<td><input type="text" name="name" value="'.htmlspecialchars($row['name']).'" size=20 maxlength=100></td>'.
		      '<td>'.htmlspecialchars($row['count']).'</td>'.
		      '<td><input type="submit" name="do" value="Ret"></td>'.
		      '<td><input type="submit" name="do" value="Tøm"></td>'.
		      '<td><input type="submit" name="do" value="Slet"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
	      '<input type="hidden" name="action" value="addlink">';
	print "<tr>\n".
	      '<td style="text-align:right;">Ny</td>'.
	      '<td>Side:<br />Feed:</td>'.
	      '<td><input type="text" name="pageurl" value="'.htmlspecialchars($row['pageurl']).'" size=50 maxlength=100><br /><input type="text" name="url" value="'.htmlspecialchars($row['url']).'" size=50 maxlength=100></td>'.
	      '<td><input type="text" name="owner" value="'.htmlspecialchars($row['owner']).'" size=20 maxlength=100><br /><input type="text" name="aut_id" value="'.htmlspecialchars($row['aut_id']).'" size=3 maxlength=10></td>'.
	      '<td><input type="text" name="name" value="'.htmlspecialchars($row['name']).'" size=20 maxlength=100></td>'.
	      '<td></td>'.
	      '<td colspan=3><input type="submit" name="do" value="Opret"></td>'.
	      "</tr>\n";
	print "</form>\n\n";


}

print "</table>\n";
print "</body>\n</html>\n";

?>
