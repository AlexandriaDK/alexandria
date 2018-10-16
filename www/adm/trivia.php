<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'trivia';

$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$fact = $_REQUEST['fact'];
$hidden = $_REQUEST['hidden'];
$id = (int) $_REQUEST['id'];
$data_id = $_REQUEST['data_id'];
$category = $_REQUEST['category'];

unset($result);

// Ret trivia
if ($action == "changetrivia" && $do != "Slet") {
	$fact = trim($fact);
	$hidden = trim($hidden);
	$q = "UPDATE trivia SET " .
	     "fact = '" . dbesc($fact) . "', " .
	     "hidden = '" . dbesc($hidden) . "' " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Trivia rettet");
	}
	$_SESSION['admin']['info'] = "Trivia rettet! " . dberror();
	rexit( $this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Slet trivia
if ($action == "changetrivia" && $do == "Slet") {
	$q = "DELETE FROM trivia WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Trivia slettet");
	}
	$_SESSION['admin']['info'] = "Trivia slettet! " . dberror();
	rexit( $this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Tilføj trivia
if ($action == "addtrivia") {
	$fact = trim($fact);
	$hidden = trim($hidden);
	$q = "INSERT INTO trivia " .
	     "(data_id, category, fact, hidden) VALUES ".
	     "('$data_id', '$category', '" . dbesc($fact) . "', '" . dbesc($hidden) . "')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id,$category,"Trivia oprettet");
	}
	$_SESSION['admin']['info'] = "Trivia oprettet! " . dberror();
	rexit( $this_type, ['category' => $category, 'data_id' => $data_id] );
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
		$mainlink = "scenarie.php?scenarie=$data_id";
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
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, fact, hidden FROM trivia WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id";
	$result = getall($query);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - trivia</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

printinfo();

print "<table align=\"center\" border=0>".
      "<tr><th colspan=5>Ret trivia for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
      "<tr>\n".
      "<th>ID</th>".
      "<th>Trivia</th>".
      "<th>Intern note</th>".
      "</tr>\n";

if ($result) {
	foreach($result AS $row) {
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
		      '<input type="hidden" name="action" value="changetrivia">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr valign=\"top\">\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><textarea cols=40 rows=3 name="fact">'.htmlspecialchars($row['fact']).'</textarea></td>'.
		      '<td><textarea cols=40 rows=3 name="hidden">'.htmlspecialchars($row['hidden']).'</textarea></td>'.
		      '<td><input type="submit" name="do" value="Ret"></td>'.
		      '<td><input type="submit" name="do" value="Slet"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
	      '<input type="hidden" name="action" value="addtrivia">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">';
	print "<tr valign=\"top\">\n".
	      '<td style="text-align:right;">Ny</td>'.
		      '<td><textarea cols=40 rows=3 id="newfact" name="fact"></textarea></td>'.
		      '<td><textarea cols=40 rows=3 name="hidden"></textarea></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Opret"></td>'.
	      "</tr>\n";
	print "</form>\n\n";

}
foreach(array("Otto-vinder: ","Otto-nominering: ", "Novellescenarie", "Grind Night-scenarie") AS $text) {
	print "<tr><td></td>";
	print "<td><a href=\"#\" onclick=\"document.getElementById('newfact').value='".$text."';\">";
	print $text;
	print "</a></td>";
	print "</tr>\n";
}
?>

</body>
</html>
