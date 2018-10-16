<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'genre';

$id = (int) $_REQUEST['id'];
$action = (string) $_REQUEST['action'];
$genid = $_REQUEST['genid'];

$title = getone("SELECT title FROM sce WHERE id = '$id'");

// Ret genre
if ($action == "changegenre") {
	doquery("DELETE FROM gsrel WHERE sce_id = '$id'");
	foreach ((array)$genid AS $gid => $value) {
		doquery("INSERT INTO gsrel (gen_id, sce_id) VALUES ('$gid','$id')");
	}
	$_SESSION['admin']['info'] = "Genrer rettet! " . dberror();
	chlog($id,'sce',"Genrer rettet");
	rexit( $this_type, [ 'id' => $id ] );
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - genrer</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

printinfo();

print "<form action=\"genre.php\" method=\"post\">\n";
print "<table align=\"center\" border=0>".
      "<tr><th colspan=3>Ret genrer for: <a href=\"scenarie.php?scenarie=$id\" accesskey=\"q\">$title</a></th></tr>\n";

$result = getall("SELECT gen.id, gen.name, gsrel.sce_id FROM gen LEFT JOIN gsrel ON gen.id = gsrel.gen_id AND sce_id = '$id' ORDER BY name");
foreach($result AS $row) {
	print "<tr>";
	print "<td><label for=\"gen_{$row['id']}\">".$row['name']."</label></td>";
	print "<td><input id=\"gen_{$row['id']}\" type=\"checkbox\" name=\"genid[".$row['id']."]\" ".($row['sce_id']?'checked="checked"':'')." /></td>";
	print "</tr>\n";
}

print "<tr><td></td><td><input type=\"submit\" value=\"Gem genrer\" /><input type=\"hidden\" name=\"action\" value=\"changegenre\" /><input type=\"hidden\" name=\"id\" value=\"$id\" /></td></tr>\n";

print "</table>\n";
print "</form>\n\n";

print "</body>\n</html>\n";

?>
