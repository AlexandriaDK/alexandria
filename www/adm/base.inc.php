<?php
setlocale(LC_TIME, "da_DK");
error_reporting(E_ALL & ~E_NOTICE);

mb_internal_encoding("UTF-8");

define("DOWNLOAD_PATH", "/home/penguin/web/loot.alexandria.dk/files/");
$ugedag = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

function getlabel ($cat, $data_id, $link = FALSE, $default = "") {
	switch ($cat) {
	
		case 'sce':
		$value = "title";
		$url = 'data?scenarie=';
		$returl = 'game.php?game=';
		break;
	
		case 'conset':
		$value = "name";
		$url = 'data?conset=';
		$returl = 'conset.php?conset=';
		break;

		case 'sys':
		$value = "name";
		$url = 'data?system=';
		$returl = 'system.php?system=';
		break;

		case 'convent':
		$value = "CONCAT(name,' (',COALESCE(year,'?'),')')";
		$url = 'data?con=';
		$returl = 'convent.php?con=';
		break;
	
		case 'aut':
		default:
		$value = "CONCAT(firstname,' ',surname)";
		$cat = 'aut';
		$url = 'data?person=';
		$returl = 'person.php?person=';
	}

	$label = getone("SELECT $value FROM $cat WHERE id = '$data_id'");
	if (!$label) $label = $default;

	if ($link == TRUE) {
		$label = '<a href="../'.$url.$data_id.'">'.$label.'</a> <a href="'.$returl.$data_id.'">[ret]</a>';
	}
	return $label;
}

function tr($tekst, $name, $def="", $opt="", $placeholder = "", $type="text", $autofocus = FALSE) {
	print "<tr valign=top><td>$tekst</td><td><input type=$type name=\"$name\" value=\"".htmlspecialchars($def)."\" placeholder=\"" . htmlspecialchars($placeholder) . "\" size=50" . ($autofocus ? " autofocus" : "") . "></td><td>$opt</td></tr>\n";
}

function tt($tekst, $name, $content = "") {
	print "<tr valign=top><td>$tekst</td><td><textarea name=\"$name\" cols=60 rows=8>\n" . htmlspecialchars($content) . "</textarea></td></tr>\n";
}

function chlog($data_id, $category, $note="") {
	global $authuser;
	if ($category == 'game') $category = 'sce';
	$authuser = $_SESSION['user_name'];
	$authuserid = $_SESSION['user_id'];
	$ip = addslashes($_SERVER['REMOTE_ADDR']);
	$ip_forward = addslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
	$user = addslashes($authuser);
	$note = addslashes($note);
	$data_id = ($data_id == NULL ? 'NULL' : (int) $data_id);
	$query = "INSERT INTO log (data_id,category,time,user,user_id,ip,ip_forward,note) " .
	         "VALUES ($data_id,'$category',NOW(),'$user','$authuserid','$ip','$ip_forward','$note')";
	$result = doquery($query);
	return $result;
}

function changelinks($data_id, $category) {
	$numlinks = getone("SELECT COUNT(*) FROM links WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Links</td><td>\n";
	$html .= sprintf("$numlinks %s",($numlinks == 1?"link":"links"));
	$html .= " - <a href=\"links.php?category=$category&amp;data_id=$data_id\" accesskey=\"l\">Edit links</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changetags($data_id, $category) {
	$tags = getcol("SELECT tag FROM tags WHERE sce_id = '$data_id'");
	$numtags = count($tags);
	$html  = "<tr valign=top><td>Tags</td><td>\n";
	$html .= sprintf("$numtags %s",($numtags == 1?"tag":"tags"));
	if ($numtags) {
		$html .= ": ";
	}
	$i = 0;
	foreach($tags AS $tag) {
		if ($i > 0) {
			$html .= ", ";
		}
		$i++;
		if ($i > 3) {
			$html .= "...";
			break;
		}
		$html .= htmlspecialchars($tag);
	}
	$html .= " - <a href=\"tags.php?category=$category&amp;data_id=$data_id\">Edit tags</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changetrivia($data_id, $category) {
	$numlinks = getone("SELECT COUNT(*) FROM trivia WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Trivia</td><td>\n";
	$html .= sprintf("$numlinks %s",($numlinks == 1?"trivia fact":"trivia facts"));
	$html .= " - <a href=\"trivia.php?category=$category&amp;data_id=$data_id\" accesskey=\"t\">Edit trivia</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changealias($data_id, $category) {
	$numlinks = getone("SELECT COUNT(*) FROM alias WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Alias</td><td>\n";
	$html .= sprintf("$numlinks %s",($numlinks == 1?"alias":"aliases"));
	$html .= " - <a href=\"alias.php?category=$category&amp;data_id=$data_id\" accesskey=\"a\">Edit aliases</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeorganizers ( $convent_id ) {
	$numlinks = getone("SELECT COUNT(*) FROM acrel WHERE convent_id = '$convent_id'");
	$html  = "<tr valign=top><td>Organizers</td><td>\n";
	$html .= "$numlinks " . ($numlinks == 1?"organizer":"organizers");
	$html .= " - <a href=\"organizers.php?category=convent&amp;data_id=$convent_id\" accesskey=\"r\">Edit organizers</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changegenre($sce_id) {
	$numgenres = getone("SELECT COUNT(*) FROM gsrel WHERE sce_id = '$sce_id'");
	$html  = "<tr valign=top><td>Genres</td><td>\n";
	$html .= sprintf("$numgenres %s",($numgenres == 1?"genre":"genres"));
	$html .= " - <a href=\"genre.php?id=$sce_id\" accesskey=\"g\">Edit genres</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changerun($sce_id) {
	$numruns = getone("SELECT COUNT(*) FROM scerun WHERE sce_id = '$sce_id'");
	$html  = "<tr valign=top><td>Runs</td><td>\n";
	$html .= sprintf("$numruns %s",($numruns == 1?"run":"runs"));
	$html .= " - <a href=\"run.php?id=$sce_id\" accesskey=\"r\">Edit runs</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changefiles($data_id, $category) {
	$numfiles = getone("SELECT COUNT(*) FROM files WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Files</td><td>\n";
	$html .= "$numfiles ".($numfiles == 1?"file":"files");
	$html .= " - <a href=\"files.php?category=$category&amp;data_id=$data_id\" accesskey=\"f\">Edit files</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeawards($convent_id) {
	list($numawards, $numnominees) = getrow("SELECT COUNT(DISTINCT a.id), COUNT(b.id) FROM award_categories a LEFT JOIN award_nominees b ON a.id = b.award_category_id WHERE convent_id = '$convent_id'");
	$html  = "<tr valign=top><td>Awards</td><td>\n";
	$html .= sprintf("$numawards %s, $numnominees %s",($numawards == 1?"award":"awards"), ($numnominees == 1?"nominated":"nominated") );
	$html .= " - <a href=\"awards.php?category=convent&amp;data_id=$convent_id\" accesskey=\"w\">Edit awards</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeuserlog($data_id, $category) {
	$numusers = getone("SELECT COUNT(DISTINCT user_id) FROM userlog WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>" . ($category == "convent" ? "Visitors" : "Users") . "</td><td>\n";
	$html .= sprintf("$numusers %s",($numusers == 1?"person":"persons"));
	$html .= " - <a href=\"userlog.php?category=$category&amp;data_id=$data_id\">Show</a>";
	$html .= "</td></tr>\n\n";
	return $html;	
}

function showpicture($data_id, $category) {
	$html = "<tr><td>Picture</td><td>";
	if (($path = getthumbnailpath($data_id, $category)) === FALSE) {
		$html .= "No";
	} else {
		$html .= "<a href=\"../$path\">Yes</a>";
	}
	$html .= "</td></tr>\n\n";
	return $html;
}

function getthumbnailpath($data_id, $category) {
	switch($category) {
		case 'convent': $folder = "convent"; break;
		case 'aut': $folder = "person"; break;
		case 'sce': $folder = "scenarie"; break;
		case 'sys': $folder = "system"; break;
		default: $folder = FALSE;
	}
	# assuming that script has chdir .. and is in webroot now
	if ($folder === FALSE || !(file_exists($path = "./gfx/$folder/l_".$data_id.".jpg")) ) {
		return FALSE;
	} else {
		return $path;
	}
}

function showtickets($data_id, $category) {
	$html = "<tr valign=\"top\"><td>Tickets</td><td>";

	$result = getall("SELECT id, user_name, submittime, status FROM updates WHERE data_id = '$data_id' AND category = '$category' ORDER BY id DESC");
        foreach($result AS $row) {
		$html .= "<a href=\"ticket.php?id={$row['id']}\">#{$row['id']}</a> - submitted by {$row['user_name']} ({$row['status']})<br>\n";
	}
	if (!$result) {
		$html .= "None";
	}
	$html .= "</td></tr>\n";
	return $html;
}

function strNullEscape($str) {
	if ($str === NULL) {
		return 'NULL';
	} else {
		if (function_exists('dbesc') ) {
			return "'" . dbesc($str) . "'";
		} else {
			return "'" . dbesc($str) . "'";
		}
	}
}

function getCount ($table, $data_id, $requiresData = FALSE, $category = "") {
	if (!$category) {
		$category = "sce";
	}
	$field = $category . "_id";
	if (!$requiresData) {
		$result = getone("SELECT COUNT(*) FROM $table WHERE $field = $data_id");
	} else {
		$result = getone("SELECT COUNT(*) FROM $table WHERE category = '$category' AND data_id = $data_id");
	}
	$count = $result;
	return $count;
}


function rexit($this_type, $dataset = [] ) {
	switch($this_type) {
		case 'convent':
		case 'conset':
		case 'organizers':
		case 'links':
		case 'files':
		case 'game':
		case 'trivia':
		case 'genre':
		case 'tag':
		case 'tags':
		case 'alias':
		case 'run':
		case 'achievements':
		case 'awards':
		case 'language':
		case 'users':
		case 'review':
				$location = $this_type . '.php';
			break;
		case 'sce':
			$location = 'game.php';
			break;
		case 'sys':
			$location = 'system.php';
			break;
		case 'aut':
			$location = 'person.php';
			break;
		default:
			$location = './';
	}
	if ($dataset) {
		$querystring = "";
		foreach($dataset AS $key => $value) {
			$querystring .= ($querystring ? "&" : "?");
			$querystring .= rawurlencode($key) . "=" . rawurlencode($value);
		}
		$location .= $querystring;
	}
	header("Location: " . $location);
	exit;

}

function printinfo() {
	if ($_SESSION['admin']['info']) {
		print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">";
		if ($_SESSION['admin']['link']) {
			print "<a href=\"" . $_SESSION['admin']['link'] . "\">";
		}
		print htmlspecialchars($_SESSION['admin']['info']);
		if ($_SESSION['admin']['link']) {
			print "</a>";
		}
		print "</font></td></tr></table>\n";
		unset($_SESSION['admin']['info']);
		unset($_SESSION['admin']['link']);
	}

}

function sqlifnull($string) {
	if ($string == "") {
		return "NULL";
	}
	return "'" . dbesc($string) . "'";
}

function htmladmstart($title = "", $headcontent = "") {
	$find = $_REQUEST['find'] ?? '';
	$htmltitle = "";
	if ($title) {
		$htmltitle = " - " . htmlspecialchars($title);
	}
	$html = <<<EOD
<!DOCTYPE html>
<html><head>
<title>Administration $htmltitle</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="/uistyle.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
$headcontent
</head>
<body>
EOD;
	print $html;
	include("links.inc.php");
	printinfo();
	return true;
}

function htmladmend() {
	$html = <<<EOD
</body>
</html>
EOD;
	print $html;
	return true;
}

function validatetoken( $token1 ) {
	$token2 = $_SESSION['token'];
	if ( ! compare_tokens( $token1, $token2 ) ) {
		print "Data *not* saved! Your token is invalid. Probably just a temporary error. Please <a href=\"../logout\">logout</a> and login again.";
		exit;
	}
}

?>
