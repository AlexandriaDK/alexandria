<?php
setlocale(LC_TIME, "da_DK");
error_reporting(E_ALL & ~E_NOTICE);

mb_internal_encoding("UTF-8");

define("DOWNLOAD_PATH", "/home/penguin/web/loot.alexandria.dk/files/");

function getlabel ($cat, $data_id, $link = FALSE, $default = "") {
	switch ($cat) {
	
		case 'sce':
		$value = "title";
		$url = 'data?scenarie=';
		$returl = 'scenarie.php?scenarie=';
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
		$label = '<a href="/'.$url.$data_id.'">'.$label.'</a> <a href="'.$returl.$data_id.'">[ret]</a>';
	}
	return $label;
}

function tr($tekst, $name, $def="", $opt="", $placeholder = "", $type="text") {
	print "<tr valign=top><td>$tekst</td><td><input type=$type name=\"$name\" value=\"".htmlspecialchars($def)."\" placeholder=\"" . htmlspecialchars($placeholder) . "\" size=50></td><td>$opt</td></tr>\n";
}

function chlog($data_id, $category, $note="") {
	global $authuser;
	$authuser = $_SESSION['user_name'];
	$authuserid = $_SESSION['user_id'];
	$ip = addslashes($_SERVER['REMOTE_ADDR']);
	$ip_forward = addslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
	$user = addslashes($authuser);
	$note = addslashes($note);
	$query = "INSERT INTO log (data_id,category,time,user,user_id,ip,ip_forward,note) " .
	         "VALUES ('$data_id','$category',NOW(),'$user','$authuserid','$ip','$ip_forward','$note')";
	$result = doquery($query);
	return $result;
}

function changelinks($data_id, $category) {
	$numlinks = getone("SELECT COUNT(*) FROM links WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Links:</td><td>\n";
	$html .= sprintf("$numlinks %s",($numlinks == 1?"link":"links"));
	$html .= " - <a href=\"links.php?category=$category&amp;data_id=$data_id\">Ret links</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changetrivia($data_id, $category) {
	$numlinks = getone("SELECT COUNT(*) FROM trivia WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Trivia:</td><td>\n";
	$html .= sprintf("$numlinks %s",($numlinks == 1?"trivia-fact":"trivia-facts"));
	$html .= " - <a href=\"trivia.php?category=$category&amp;data_id=$data_id\">Ret trivia</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changealias($data_id, $category) {
	$numlinks = getone("SELECT COUNT(*) FROM alias WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Alias:</td><td>\n";
	$html .= sprintf("$numlinks %s",($numlinks == 1?"alias":"aliaser"));
	$html .= " - <a href=\"alias.php?category=$category&amp;data_id=$data_id\">Ret alias</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeorganizers ( $convent_id ) {
	$numlinks = getone("SELECT COUNT(*) FROM acrel WHERE convent_id = '$convent_id'");
	$html  = "<tr valign=top><td>Arrangører:</td><td>\n";
	$html .= "$numlinks " . ($numlinks == 1?"arrangør":"arrangører");
	$html .= " - <a href=\"organizers.php?category=convent&amp;data_id=$convent_id\" accesskey=\"r\">Ret arrangører</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changegenre($sce_id) {
	$numgenres = getone("SELECT COUNT(*) FROM gsrel WHERE sce_id = '$sce_id'");
	$html  = "<tr valign=top><td>Genrer:</td><td>\n";
	$html .= sprintf("$numgenres %s",($numgenres == 1?"genre":"genrer"));
	$html .= " - <a href=\"genre.php?id=$sce_id\" accesskey=\"g\">Ret genrer</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changerun($sce_id) {
	$numruns = getone("SELECT COUNT(*) FROM scerun WHERE sce_id = '$sce_id'");
	$html  = "<tr valign=top><td>Afviklinger:</td><td>\n";
	$html .= sprintf("$numruns %s",($numruns == 1?"afvikling":"afviklinger"));
	$html .= " - <a href=\"run.php?id=$sce_id\" accesskey=\"r\">Ret afviklinger</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changefiles($data_id, $category) {
	$numfiles = getone("SELECT COUNT(*) FROM files WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>Filer:</td><td>\n";
	$html .= "$numfiles ".($numfiles == 1?"fil":"filer");
	$html .= " - <a href=\"files.php?category=$category&amp;data_id=$data_id\" accesskey=\"f\">Ret filer</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeawards($convent_id) {
	list($numawards, $numnominees) = getrow("SELECT COUNT(DISTINCT a.id), COUNT(b.id) FROM award_categories a LEFT JOIN award_nominees b ON a.id = b.award_category_id WHERE convent_id = '$convent_id'");
	$html  = "<tr valign=top><td>Priser:</td><td>\n";
	$html .= sprintf("$numawards %s, $numnominees %s",($numawards == 1?"pris":"priser"), ($numnominees == 1?"nomineret":"nominerede") );
	$html .= " - <a href=\"awards.php?category=convent&amp;data_id=$convent_id\" accesskey=\"w\">Ret priser</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeuserlog($data_id, $category) {
	$numusers = getone("SELECT COUNT(DISTINCT user_id) FROM userlog WHERE data_id = '$data_id' AND category = '$category'");
	$html  = "<tr valign=top><td>" . ($category == "convent" ? "Besøgende" : "Brugere") . ":</td><td>\n";
	$html .= sprintf("$numusers %s",($numusers == 1?"person":"personer"));
	$html .= " - <a href=\"userlog.php?category=$category&amp;data_id=$data_id\">Vis</a>";
	$html .= "</td></tr>\n\n";
	return $html;	
}

function showpicture($data_id, $category) {
	$html = "<tr valign=\"top\"><td>Billede:</td><td>";
	if (($path = getthumbnailpath($data_id, $category)) === FALSE) {
		$html .= "Nej";
	} else {
		$html .= "<a href=\"$path\">Ja</a>";
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
	if ($folder === FALSE || !(file_exists($path = "../gfx/$folder/l_".$data_id.".jpg")) ) {
		return FALSE;
	} else {
		return $path;
	}
}


function showtickets($data_id, $category) {
	$html = "<tr valign=\"top\"><td>Tickets:</td><td>";

	$result = getall("SELECT id, user_name, submittime, status FROM updates WHERE data_id = '$data_id' AND category = '$category' ORDER BY id DESC");
        foreach($result AS $row) {
		$html .= "<a href=\"ticket.php?id={$row['id']}\">#{$row['id']}</a> - indsendt af {$row['user_name']} ({$row['status']})<br>\n";
	}
	if (!$result) {
		$html .= "Ingen";
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

$ugedag = array("søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag");

/*
function pubdateprint($datetime) {
	$pubdatetime = strtotime($datetime);
	$start = strftime('%e. %B, %R',$pubdatetime);
	if (date("Y-m-d",$pubdatetime) == date("Y-m-d")) {
		$start = "I dag, ".date("H:i",$pubdatetime);
	} elseif (date("Y-m-d",$pubdatetime) == date("Y-m-d",strtotime("- 1 day")) ) {
		$start = "I går, ".date("H:i",$pubdatetime);
	} elseif (date("Y-m-d",$pubdatetime) == date("Y-m-d",strtotime("- 2 days")) ) {
		$start = "I forgårs, ".date("H:i",$pubdatetime);
	} else {
		$start = strftime('%e. %B %Y, %R',$pubdatetime);
	}
	return $start;
}
*/

function rexit($this_type, $dataset = [] ) {
	switch($this_type) {
		case 'convent':
			$location = 'convent.php';
			break;
		case 'conset':
			$location = 'conset.php';
			break;
		case 'organizers':
			$location = 'organizers.php';
			break;
		case 'links':
			$location = 'links.php';
			break;
		case 'files':
			$location = 'files.php';
			break;
		case 'sce':
			$location = 'scenarie.php';
			break;
		case 'sys':
			$location = 'system.php';
			break;
		case 'aut':
			$location = 'person.php';
			break;
		case 'trivia':
			$location = 'trivia.php';
			break;
		case 'alias':
			$location = 'alias.php';
			break;
		case 'genre':
			$location = 'genre.php';
			break;
		case 'run':
			$location = 'run.php';
			break;
		case 'achievements':
			$location = 'achievements.php';
			break;
		case 'awards':
			$location = 'awards.php';
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
		print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">" . htmlspecialchars($_SESSION['admin']['info']) . "</font></td></tr></table>\n";
		unset($_SESSION['admin']['info']);
	}

}

function sqlifnull($string) {
	if ($string == "") {
		return "NULL";
	}
	return "'" . dbesc($string) . "'";
}

function htmladmstart($title = "") {
	$htmltitle = "";
	if ($title) {
		$htmltitle = " - " . htmlspecialchars($title);
	}
	$html = <<<EOD
<!DOCTYPE html>
<html><head>
<title>Administration $htmltitle</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
EOD;
	print $html;
	include("links.inc");
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

?>
