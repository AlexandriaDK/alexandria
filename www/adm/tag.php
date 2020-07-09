<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'tag';

$tag_id = (int) $_REQUEST['tag_id'];
$action = (string) $_REQUEST['action'];
$tag = (string) $_REQUEST['tag'];
$description = (string) $_REQUEST['description'];

if ( $action ) {
	validatetoken( $token );
}

if ($tag && !$tag_id) {
	$tag_id = getone("SELECT id FROM tag WHERE tag = '" . dbesc($tag) . "'");
}

if (!$action && $tag_id) {
	list($tag, $description) = getrow("SELECT tag, description FROM tag WHERE id = $tag_id");
}

if ($action == "Remove" && $tag_id) {
	$tag = getone("SELECT tag FROM tag WHERE id = $tag_id");
	$q = "DELETE FROM tag WHERE id = $tag_id";
	$r = doquery($q);

	if ($r) {
		chlog($tag_id,$this_type,"Tag-beskrivelse slettet: $tag");
	}
	$_SESSION['admin']['info'] = "Tag description removed! " . dberror();
	rexit($this_type);

}

if ($action == "update" && $tag_id) {
	$q = "UPDATE tag SET " .
	     "tag = '".dbesc($tag)."', ".
	     "description = '".dbesc($description)."' ".
	     "WHERE id = '$tag_id'";
	$r = doquery($q);
	if ($r) {
		chlog($tag_id,$this_type,"Tag-beskrivelse rettet");
	}
	$_SESSION['admin']['info'] = "Tag description updated! " . dberror();
	rexit( $this_type, [ 'tag_id' => $tag_id ] );
}

if ($action == "create") {
	$tid = getone("SELECT id FROM tag WHERE tag = '" . dbesc($tag) . "'");
	if ($tid) {
		$_SESSION['admin']['info'] = "This tag already exists!";
	} elseif (!$tag) {
		$_SESSION['admin']['info'] = "Tag name missing!";
	} else {
		$q = "INSERT INTO tag (tag, description) " .
		     "VALUES ( ".
			 "'".dbesc($tag)."', ".
			 "'".dbesc($description)."' ".
			 ")";
		$r = doquery($q);
		if ($r) {
			$tag_id = dbid();
			chlog($tag_id,$this_type,"Tag-beskrivelse oprettet");
		}
		$_SESSION['admin']['info'] = "Tag description created! " . dberror();
		rexit( $this_type, [ 'tag_id' => $tag_id ] );
	}
}

htmladmstart("Tag");

print "<FORM ACTION=\"tag.php\" METHOD=\"post\">\n";
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
if (!$tag_id) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"update\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"tag_id\" value=\"$tag_id\">\n";
}

print "<a href=\"tag.php\">New tag description</a>";

print "<table border=0>\n";

if ($tag_id) {
	$tag = getone("SELECT tag FROM tag WHERE id = $tag_id");
	print "<tr><td>ID</td><td>$tag_id - <a href=\"../data?tag=" . rawurlencode($tag) . "\" accesskey=\"q\">Show tag page</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$tag_id\">Show log</a>";
	}
	print "\n</td></tr>\n";
}

tr("Tag","tag",$tag);
print "<tr valign=top><td>Description</td><td><textarea name=description cols=100 rows=25>\n" . htmlspecialchars($description) . "</textarea></td></tr>\n";


print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($tag_id ? "Update" : "Create").' tag description">' . ($tag_id ? ' <input type="submit" name="action" value="Remove" onclick="return confirm(\'Remove tag description?\n\nOnly the description of the tag will be deleted. The tag will still be present for any scenario with this tag.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($tag_id) {
// Mulighed for at rette links
	print changelinks($tag_id,$this_type);

// Mulighed for at rette trivia
	print changetrivia($tag_id,$this_type);

// Mulighed for at rette alias
//	print changealias($tag_id,$this_type);

// Vis evt. billede
//	print showpicture($tag_id,$this_type);

// Vis tickets
	print showtickets($tag_id,$this_type);

// Scenarier under dette tag
	$q = getall("SELECT sce.id, sce.title FROM sce INNER JOIN tags ON sce.id = tags.sce_id WHERE tag = '" . dbesc($tag) . "' ORDER BY sce.title, sce.id");
	print "<tr valign=top><td align=right>Contains the<br>following scenarios</td><td>\n";
	foreach($q AS list($id, $title) ) {
		print "<a href=\"game.php?game=$id\">$title</a><br>";
	}
	if (!$q) print "[None]";
	print "</td></tr>\n";
} elseif ($tag) {
	$q = getall("SELECT sce.id, sce.title FROM sce INNER JOIN tags ON sce.id = tags.sce_id WHERE tag = '" . dbesc($tag) . "' ORDER BY sce.title, sce.id");
	print "<tr valign=top><td align=right>Contains the<br>following scenarios</td><td>\n";
	foreach($q AS list($id, $title) ) {
		print "<a href=\"game.php?game=$id\">$title</a><br>";
	}
	if (!$q) print "[None]";
	print "</td></tr>\n";

}

?>

</table>

</form>

<hr size=1>

<form action="tag.php" method="get">
Tags with descriptions 
<select name="tag_id">
<?php
$q = getall("SELECT COUNT(tags.id) AS count, tag.id, tag.tag FROM tag LEFT JOIN tags ON tag.tag = tags.tag GROUP BY tag.id, tag.tag ORDER BY tag");
foreach($q AS $r) {
	print "<option value=$r[id]";
	if ($r['id'] == $tag_id) print " SELECTED";
	print ">" . htmlspecialchars($r['tag']) . " (" . $r['count'] . ")\n";
}
?>
</select>
<input type=submit value="Edit">
</form>

<form action="tag.php" method="get">
All tags
<select name="tag">
<?php
$q = getall("SELECT COUNT(tags.id) AS count, tags.tag, tag.id AS tag_id FROM tags LEFT JOIN tag ON tags.tag = tag.tag GROUP BY tags.tag ORDER BY tag");
foreach($q AS $r) {
	print "<option " . ($r['tag_id'] ? 'class="existing"' : '')  . " value=\"" . htmlspecialchars($r['tag']) . "\">" . htmlspecialchars($r['tag']) . " (" . $r['count'] . ")\n";
}

?>
</select>
<input type=submit value="Edit">
</form>

</body>
</html>
