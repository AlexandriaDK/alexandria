<?php
$admonly = TRUE;
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'news';

unset($result);
$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$text = $_REQUEST['text'];
$online = $_REQUEST['online'];
$published = $_REQUEST['published'];
$id = $_REQUEST['id'];

// Ret news
if ($action == "changenews" && $do != "Slet") {
	$text = trim($text);
	$online = ($online == "on" ? 1 : 0);
	$q = "UPDATE news SET " .
	     "text = '" . dbesc($text) . "', " .
	     "online = '$online', " .
	     "published = '$published' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	$info = "News rettet! " . dberror();
	$id = "";
}

// Slet alias
if ($action == "changenews" && $do == "Slet") {
	$q = "DELETE FROM news WHERE id = '$id'";
	$r = doquery($q);
	$info = "News slettet! " . dberror();
	$id = "";
}

// Tilføj alias
if ($action == "addnews") {
	$text = trim($text);
	if (!$published) $published = date("Y-m-d H:i:s");
	$online = ($online == "on" ? 1 : 0);
	$q = "INSERT INTO news " .
	     "(text, published, online) VALUES ".
	     "('" . dbesc($text) . "', '$published', '$online')";
	$r = doquery($q);
	$info = "News oprettet! " . dberror();
}

if ($id) {
	$row = getrow("SELECT text, online, published FROM news WHERE id = '$id'");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - nyheder</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

if ($id) {
	$selected = ($row['online'] == 1 ? 'checked="checked"' : '');
	print '<form action="news.php" method="post">'.
	      '<input type="hidden" name="action" value="changenews">'.
	      '<input type="hidden" name="id" value="'.$id.'">';
	print '<p>News ID #'.$id.'</p>';
	print '<p><textarea name="text" cols="60" rows="5">'.htmlspecialchars($row['text']).'</textarea></p>';
	print '<p>Dato: <input type="text" name="published" length="20" value="'.$row['published'].'" placeholder="(efterlad blank for d.d.)"></p>';
	print '<p>Online: <input type="checkbox" name="online" '.$selected.'></p>';
	print '<p><input type="submit" name="do" value="Ret"></p>'.
		    '<p><input type="submit" name="do" value="Slet"></p>';
	print "</form>\n\n";
} else {
	print '<form action="news.php" method="post">'.
	      '<input type="hidden" name="action" value="addnews">';
	print '<p>Ny nyhed:</p>';
	print '<p><textarea name="text" cols="60" rows="5"></textarea></p>';
	print '<p>Dato: <input type="text" name="published" length="20" placeholder="(efterlad blank for d.d.)"></p>';
	print '<p>Online: <input type="checkbox" name="online" checked="checked"></p>';
	print '<p><input type="submit" name="do" value="Opret"></p>';

	$result = getall("SELECT id, text, online FROM news ORDER BY id DESC");
	foreach($result AS $row) {
		print '<p>';
		if ($row['online']) print "<b>";
		print '<a href="news.php?id='.$row['id'].'">'.$row['id'].'</a> '.htmlspecialchars($row['text']).'</a>';
		if ($row['online']) print "</b>";
		print '</p>';
	}
}

print "</body>\n</html>\n";

?>
