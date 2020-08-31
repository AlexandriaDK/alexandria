<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'news';

unset($result);
$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$text = $_REQUEST['text'];
$online = $_REQUEST['online'];
$published = $_REQUEST['published'];
$id = $_REQUEST['id'];

if ( $action ) {
	validatetoken( $token );
}

// Ret news
if ($action == "changenews" && $do != "Delete") {
	$text = trim($text);
	if (!$published) $published = date("Y-m-d H:i:s");
	$online = ($online == "on" ? 1 : 0);
	$q = "UPDATE news SET " .
	     "text = '" . dbesc($text) . "', " .
	     "online = '$online', " .
	     "published = '$published' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	$info = "News updated! " . dberror();
	$id = "";
}

// Delete news
if ($action == "changenews" && $do == "Delete") {
	$q = "DELETE FROM news WHERE id = '$id'";
	$r = doquery($q);
	$info = "News deleted! " . dberror();
	$id = "";
}

// Add news
if ($action == "addnews") {
	$text = trim($text);
	if (!$published) $published = date("Y-m-d H:i:s");
	$online = ($online == "on" ? 1 : 0);
	$q = "INSERT INTO news " .
	     "(text, published, online) VALUES ".
	     "('" . dbesc($text) . "', '$published', '$online')";
	$r = doquery($q);
	$info = "News created! " . dberror();
}

if ($id) {
	$row = getrow("SELECT text, online, published FROM news WHERE id = '$id'");
}

htmladmstart("News");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

if ($id) {
	$selected = ($row['online'] == 1 ? 'checked="checked"' : '');
	print '<form action="news.php" method="post">'.
	      '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
	      '<input type="hidden" name="action" value="changenews">'.
	      '<input type="hidden" name="id" value="'.$id.'">';
	print '<p>News ID #'.$id.'</p>';
	print '<p>News<br><textarea name="text" cols="60" rows="5" placeholder="Short news post in English only. [[[p1|Alexandria links]]] are allowed as well as HTML.">'.htmlspecialchars($row['text']).'</textarea></p>';
	print '<p>Date and time <input type="text" name="published" length="20" value="'.$row['published'].'" placeholder="(leave blank for today)"></p>';
	print '<p>Online <input type="checkbox" name="online" '.$selected.'></p>';
	print '<p><input type="submit" name="do" value="Edit"></p>'.
		    '<p><input type="submit" name="do" value="Delete"></p>';
	print "</form>\n\n";
} else {
	print '<form action="news.php" method="post">'.
	      '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
	      '<input type="hidden" name="action" value="addnews">';
	print '<p>New post:</p>';
	print '<p>News<br><textarea name="text" cols="60" rows="5" placeholder="Short news post in English only. [[[p1|Alexandria links]]] are allowed as well as HTML."></textarea></p>';
	print '<p>Date and time <input type="text" name="published" length="20" placeholder="(leave blank for today)"></p>';
	print '<p>Online <input type="checkbox" name="online" checked="checked"></p>';
	print '<p><input type="submit" name="do" value="Create"></p>';

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
