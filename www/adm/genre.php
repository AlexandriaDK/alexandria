<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'genre';

$id = (int) $_REQUEST['id'];
$action = (string) $_REQUEST['action'];
$genid = (array) $_REQUEST['genid'];

$title = getone("SELECT title FROM sce WHERE id = '$id'");

// Ret genre
if ($action == "changegenre") {
	doquery("DELETE FROM gsrel WHERE sce_id = '$id'");
	foreach ($genid AS $gid => $value) {
		doquery("INSERT INTO gsrel (gen_id, sce_id) VALUES ('$gid','$id')");
	}
	$_SESSION['admin']['info'] = "Genres for scenario updated! " . dberror();
	chlog($id,'sce',"Genrer rettet");
	rexit( $this_type, [ 'id' => $id ] );
}

htmladmstart("Genre");

$result = getall("SELECT gen.id, gen.name, gsrel.sce_id FROM gen LEFT JOIN gsrel ON gen.id = gsrel.gen_id AND sce_id = '$id' ORDER BY name");

if ($id) {
	print "<form action=\"genre.php\" method=\"post\">\n";
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=3>Set genres for: <a href=\"scenarie.php?scenarie=$id\" accesskey=\"q\">$title</a></th></tr>\n";

	foreach($result AS $row) {
		print "<tr>";
		print "<td><label for=\"gen_{$row['id']}\">".$row['name']."</label></td>";
		print "<td><input id=\"gen_{$row['id']}\" type=\"checkbox\" name=\"genid[".$row['id']."]\" ".($row['sce_id']?'checked="checked"':'')." /></td>";
		print "</tr>\n";
	}

	print "<tr><td></td><td><input type=\"submit\" value=\"Save genres\" /><input type=\"hidden\" name=\"action\" value=\"changegenre\" /><input type=\"hidden\" name=\"id\" value=\"$id\" /></td></tr>\n";

	print "</table>\n";
	print "</form>\n\n";
} else {
	print "Error: No data id provided.";
}

print "</body>\n</html>\n";

?>
