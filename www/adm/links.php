<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'links';

$action = $_REQUEST['action'];
$do = $_REQUEST['do'];
$url = $_REQUEST['url'];
$description = $_REQUEST['description'];
$id = $_REQUEST['id'];
$category = $_REQUEST['category'];
$data_id = $_REQUEST['data_id'];

$url = trim($url);
if ($url && substr($url,0,4) != 'http') {
	$url = 'http://' . $url;
}

function rlyehlink ($text) {
	if (preg_match('/^r(lyeh)?(\d+)/i',$text,$regs)) {
		return $regs[2];
	} else {
		return false;
	}
}

unset($result);

// Ret link
if ($action == "changelink" && $do != "Slet") {
	$url = trim($url);
	$description = trim($description);
	if ($rid = rlyehlink($url)) {
		$url = "http://rlyeh.alexandria.dk/pub/scenarier/scenarie.php3?id=".$rid;
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
	$_SESSION['admin']['info'] = "Link rettet! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Slet link
if ($action == "changelink" && $do == "Slet") {
	$q = "DELETE FROM links WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id,$category,"Link slettet");
	}
	$_SESSION['admin']['info'] = "Link slettet! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id] );
}

// Tilføj link
if ($action == "addlink") {
	$url = trim($url);
	$description = trim($description);
	if ($rid = rlyehlink($url)) {
		$url = "http://rlyeh.alexandria.dk/pub/scenarier/scenarie.php3?id=".$rid;
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
	$_SESSION['admin']['info'] = "Link oprettet! " . dberror();
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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - links</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

printinfo();

if ($data_id && $category) {
	print "<table align=\"center\" border=0>".
	      "<tr><th colspan=5>Ret links for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>URL</th>".
	      "<th>Beskrivelse</th>".
	      "</tr>\n";

	foreach($result AS $row) {
		print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
		      '<input type="hidden" name="action" value="changelink">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td><input type="text" name="url" value="'.htmlspecialchars($row['url']).'" size=40 maxlength=100></td>'.
		      '<td><input type="text" name="description" value="'.htmlspecialchars($row['description']).'" size=40 maxlength=100></td>'.
		      '<td><input type="submit" name="do" value="Ret"></td>'.
		      '<td><input type="submit" name="do" value="Slet"></td>'.
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'.
	      '<input type="hidden" name="action" value="addlink">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">';
	print "<tr>\n".
	      '<td style="text-align:right;">Ny</td>'.
	      '<td><input type="text" name="url" value="" size=40 maxlength=100></td>'.
	      '<td><input type="text" name="description" value="" size=40 maxlength=100></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Opret"></td>'.
	      "</tr>\n";
	print "</form>\n\n";

	print "</table>\n";
} else {
	print "Fejl: Intet data-id angivet.";
}


print "</body>\n</html>\n";

?>
