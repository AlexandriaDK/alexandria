<?php
setlocale(LC_TIME, "da_DK");
error_reporting(E_ALL & ~E_NOTICE);

mb_internal_encoding("UTF-8");

define("DOWNLOAD_PATH", "/home/penguin/web/loot.alexandria.dk/files/");

function getlabel($category, $data_id, $link = FALSE, $default = "")
{
	switch ($category) {
		case 'game':
			$value = "title";
			$url = 'data?scenarie=';
			$returl = 'game.php?game=';
			break;

		case 'conset':
			$value = "name";
			$url = 'data?conset=';
			$returl = 'conset.php?conset=';
			break;

		case 'gamesystem':
			$value = "name";
			$url = 'data?system=';
			$returl = 'gamesystem.php?gamesystem=';
			break;

		case 'convention':
			$value = "CONCAT(name,' (',COALESCE(year,'?'),')')";
			$url = 'data?con=';
			$returl = 'convention.php?con=';
			break;

		case 'issue':
			$value = "title";
			$url = 'magazines?issue=';
			$returl = 'magazine.php?issue_id=';
			break;

		case 'magazine':
			$value = "name";
			$url = 'magazines?id=';
			$returl = 'magazine.php?magazine_id=';
			break;

		case 'tag':
			$value = "tag";
			$url = 'data?tag=';
			$returl = 'tag.php?tag_id=';
			break;
	
		case 'person':
		default:
			$value = "CONCAT(firstname,' ',surname)";
			$category = 'person';
			$url = 'data?person=';
			$returl = 'person.php?person=';
	}

	$label = getone("SELECT $value FROM $category WHERE id = '$data_id'");
	if (!$label) $label = $default;

	if ($link == TRUE) {
		$label = '<a href="../' . $url . ($category == 'tag' ? rawurlencode($label) : $data_id) . '">' . $label . '</a> <a href="' . $returl . $data_id . '" accesskey="q">[edit]</a>';
	}
	return $label;
}

function tr($tekst, $name, $def = "", $opt = "", $placeholder = "", $type = "text", $autofocus = FALSE)
{
	print "<tr valign=top><td>$tekst</td><td><input type=$type name=\"$name\" value=\"" . htmlspecialchars($def ?? '') . "\" placeholder=\"" . htmlspecialchars($placeholder ?? '') . "\" size=50" . ($autofocus ? " autofocus" : "") . "></td><td>$opt</td></tr>\n";
}

function tt($tekst, $name, $content = "")
{
	print "<tr valign=top><td>$tekst</td><td><textarea name=\"$name\" cols=60 rows=8>\n" . htmlspecialchars($content) . "</textarea></td></tr>\n";
}

function chlog($data_id, $category, $note = "")
{
	global $authuser;
	$authuser = $_SESSION['user_name'];
	$authuserid = $_SESSION['user_id'];
	$user = dbesc($authuser);
	$note = dbesc($note);
	$data_id = ($data_id == NULL ? 'NULL' : (int) $data_id);
	$query = "INSERT INTO log (data_id,category,time,user,user_id,note) " .
		"VALUES ($data_id,'$category',NOW(),'$user','$authuserid','$note')";
	$result = doquery($query);
	return $result;
}

function changelinks($data_id, $category)
{
	$data_field = getFieldFromCategory($category);
	$numlinks = getone("SELECT COUNT(*) FROM links WHERE `$data_field` = '$data_id'");
	$html  = "<tr valign=top><td>Links</td><td>\n";
	$html .= sprintf("$numlinks %s", ($numlinks == 1 ? "link" : "links"));
	$html .= " - <a href=\"links.php?category=$category&amp;data_id=$data_id\" accesskey=\"l\">Edit links</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changetags($data_id, $category)
{
	$tags = getcol("SELECT tag FROM tags WHERE game_id = '$data_id'");
	$numtags = count($tags);
	$html  = "<tr valign=top><td>Tags</td><td>\n";
	$html .= sprintf("$numtags %s", ($numtags == 1 ? "tag" : "tags"));
	if ($numtags) {
		$html .= ": ";
	}
	$i = 0;
	foreach ($tags as $tag) {
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

function changetrivia($data_id, $category)
{
	$data_field = getFieldFromCategory($category);
	$numlinks = getone("SELECT COUNT(*) FROM trivia WHERE `$data_field` = '$data_id'");
	$html  = "<tr valign=top><td>Trivia</td><td>\n";
	$html .= sprintf("$numlinks %s", ($numlinks == 1 ? "trivia fact" : "trivia facts"));
	$html .= " - <a href=\"trivia.php?category=$category&amp;data_id=$data_id\" accesskey=\"t\">Edit trivia</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changealias($data_id, $category)
{
	$data_field = getFieldFromCategory($category);
	$numlinks = getone("SELECT COUNT(*) FROM alias WHERE `$data_field` = '$data_id'");
	$html  = "<tr valign=top><td>Alias</td><td>\n";
	$html .= sprintf("$numlinks %s", ($numlinks == 1 ? "alias" : "aliases"));
	$html .= " - <a href=\"alias.php?category=$category&amp;data_id=$data_id\" accesskey=\"a\">Edit aliases</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeorganizers($convention_id)
{
	$numlinks = getone("SELECT COUNT(*) FROM pcrel WHERE convention_id = '$convention_id'");
	$html  = "<tr valign=top><td>Organizers</td><td>\n";
	$html .= "$numlinks " . ($numlinks == 1 ? "organizer" : "organizers");
	$html .= " - <a href=\"organizers.php?category=convention&amp;data_id=$convention_id\" accesskey=\"r\">Edit organizers</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changegenre($game_id)
{
	$numgenres = getone("SELECT COUNT(*) FROM ggrel WHERE game_id = '$game_id'");
	$html  = "<tr valign=top><td>Genres</td><td>\n";
	$html .= sprintf("$numgenres %s", ($numgenres == 1 ? "genre" : "genres"));
	$html .= " - <a href=\"genre.php?id=$game_id\" accesskey=\"g\">Edit genres</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changerun($game_id)
{
	$numruns = getone("SELECT COUNT(*) FROM gamerun WHERE game_id = '$game_id'");
	$html  = "<tr valign=top><td>Runs</td><td>\n";
	$html .= sprintf("$numruns %s", ($numruns == 1 ? "run" : "runs"));
	$html .= " - <a href=\"run.php?id=$game_id\" accesskey=\"r\">Edit runs</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changefiles($data_id, $category)
{
	$data_field = getFieldFromCategory($category);
	$dbfiles = getone("SELECT COUNT(*) FROM files WHERE `$data_field` = '$data_id'");
	$dirfiles = count(glob(DOWNLOAD_PATH . getcategorydir($category) . "/" . $data_id . "/*"));
	$textfiles = "0 files";
	if ($dirfiles || $dbfiles) {
		$textfiles = "<span title=\"Files registered in database\">$dbfiles</span>/<span title=\"Files uploaded\">$dirfiles</span> " . ($dbfiles == 1 && $dirfiles == 1 ? "file" : "files");
	}
	$html  = "<tr valign=top><td>Files</td><td>\n";
	$html .= $textfiles;
	$html .= " - <a href=\"files.php?category=$category&amp;data_id=$data_id\" accesskey=\"f\">Edit files</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeawards($data_id, $category)
{
	$type_field = $category . '_id';
	list($numawards, $numnominees) = getrow("SELECT COUNT(DISTINCT a.id), COUNT(b.id) FROM award_categories a LEFT JOIN award_nominees b ON a.id = b.award_category_id WHERE `$type_field` = $data_id");
	$html  = "<tr valign=top><td>Awards</td><td>\n";
	$html .= sprintf("$numawards %s, $numnominees %s", ($numawards == 1 ? "award" : "awards"), ($numnominees == 1 ? "nominated" : "nominated"));
	$html .= " - <a href=\"awards.php?category=$category&amp;data_id=$data_id\" accesskey=\"w\">Edit awards</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function changeuserlog($data_id, $category)
{
	$data_field = getFieldFromCategory($category);
	$numusers = getone("SELECT COUNT(DISTINCT user_id) FROM userlog WHERE `$data_field` = '$data_id'");
	$html  = "<tr valign=top><td>" . ($category == "convention" ? "Visitors" : "Users") . "</td><td>\n";
	$html .= sprintf("$numusers %s", ($numusers == 1 ? "person" : "persons"));
	$html .= " - <a href=\"userlog.php?category=$category&amp;data_id=$data_id\">Show</a>";
	$html .= "</td></tr>\n\n";
	return $html;
}

function showpicture($data_id, $category)
{
	$html = "<tr><td>Picture</td><td>";
	if (($path = getthumbnailpath($data_id, $category)) === FALSE) {
		$html .= "No";
	} else {
		$html .= "<a href=\"../$path\">Yes</a>";
	}
	$html .= "</td></tr>\n\n";
	return $html;
}

function getthumbnailpath($data_id, $category)
{
	$folder = getcategorythumbdir($category);

	# assuming that script has chdir .. and is in webroot now
	if ($folder === FALSE || !(file_exists($path = "./gfx/$folder/l_" . $data_id . ".jpg"))) {
		return FALSE;
	} else {
		return $path;
	}
}

function showtickets($data_id, $category)
{
	$html = "<tr valign=\"top\"><td>Tickets</td><td>";

	$result = getall("SELECT id, user_name, submittime, status FROM updates WHERE data_id = '$data_id' AND category = '$category' ORDER BY id DESC");
	foreach ($result as $row) {
		$html .= "<a href=\"ticket.php?id={$row['id']}\">#{$row['id']}</a> - submitted by {$row['user_name']} ({$row['status']})<br>\n";
	}
	if (!$result) {
		$html .= "None";
	}
	$html .= "</td></tr>\n";
	return $html;
}

function strNullEscape($str)
{
	if ($str === NULL) {
		return 'NULL';
	} else {
		if (function_exists('dbesc')) {
			return "'" . dbesc($str) . "'";
		} else {
			return "'" . dbesc($str) . "'";
		}
	}
}

function getCount($table, $data_id, $requiresCategoryAndData = FALSE, $category = "")
{
	if (!$category) {
		$category = "game";
	}
	$field = $category . "_id";
	if (!$requiresCategoryAndData) {
		$result = getone("SELECT COUNT(*) FROM $table WHERE $field = $data_id");
	} else {
		$result = getone("SELECT COUNT(*) FROM $table WHERE category = '$category' AND data_id = $data_id");
	}
	$count = $result;
	return $count;
}

function autidextra($person)
{
	$person_id = (int) $person;
	$person_extra = ($person_id ? '' : trim($person));
	return ['id' => $person_id, 'extra' => $person_extra];
}

function rexit($this_type, $dataset = [])
{
	switch ($this_type) {
		case 'convention':
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
		case 'magazine':
		case 'review':
		case 'person':
		case 'gamesystem':
			$location = $this_type . '.php';
			break;
		default:
			$location = './';
	}
	if ($dataset) {
		$querystring = "";
		foreach ($dataset as $key => $value) {
			$querystring .= ($querystring ? "&" : "?");
			$querystring .= rawurlencode($key) . "=" . rawurlencode($value);
		}
		$location .= $querystring;
	}
	header("Location: " . $location);
	exit;
}

function mojibakefix($string)
{
	if (is_null($string))
	{
		return '';
	}
	$from = ['â€™', 'â€œ', 'â€', 'â€¦', 'Ã©', 'â€“', 'â€”', 'Ã§', 'Ã»'];
	$to = ['\'', '"', '"', '…', 'é', '–', '—', 'ç', 'û'];
	return str_replace($from, $to, $string);
}

function printinfo()
{
	$info = $_SESSION['admin']['info'] ?? FALSE;
	$link = $_SESSION['admin']['link'] ?? FALSE;
	if ($info) {
		print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">";
		if ($link) {
			print "<a href=\"" . $_SESSION['admin']['link'] . "\">";
		}
		print htmlspecialchars($_SESSION['admin']['info']);
		if ($link) {
			print "</a>";
		}
		print "</font></td></tr></table>\n";
		unset($_SESSION['admin']['info']);
		unset($_SESSION['admin']['link']);
	}
}

function sqlifnull($string)
{
	if ($string == "" || $string == 0) {
		return "NULL";
	}
	return "'" . dbesc($string) . "'";
}

function invaliddate($string)
{
	if ($string == "") {
		return false;
	}
	$parts = explode("-", $string);
	return !checkdate($parts[1], $parts[2], $parts[0]);
}

function strSplitParticipants($str)
{
	$str = trim($str);
	if (!preg_match('/^(\d+)\s*([–-]\s*(\d+))?$/u', $str, $match)) {
		return [NULL, NULL];
	}
	$str_min = $match[1] ?? '';
	$str_max = $match[3] ?? '';
	if (!$str_max) {
		$str_max = $str_min;
	} elseif ($str_min > $str_max) {
		$str_tmp = $str_min;
		$str_min = $str_max;
		$str_max = $str_tmp;
	}
	return [$str_min, $str_max];
}

function getCategoryFromShort($short)
{
	$categorymap = [
		'c' => 'convention',
		'cs' => 'conset',
		'tag' => 'tag',
		'sys' => 'gamesystem',
		'p' => 'person',
		'm' => 'magazine',
		'i' => 'issue',
		'g' => 'game',
	];
	return $categorymap[$short];
}

function getShortFromCategory($category)
{
	$categorymap = [
		'convention' => 'c',
		'conset' => 'cs',
		'tag' => 'tag',
		'gamesystem' => 'sys',
		'person' => 'p',
		'magazine' => 'm',
		'issue' => 'i',
		'sce' => 'g',
		'game' => 'g'
	];
	return $categorymap[$category];
}

function htmladmstart($title = "", $headcontent = "")
{
	$htmltitle = "";
	if ($title) {
		$htmltitle = " - " . htmlspecialchars($title);
	}
	$html = <<<EOD
<!DOCTYPE html>
<html><head>
<title>Editor $htmltitle</title>
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
<script src="adm.js"></script>
$headcontent
</head>
<body>
EOD;
	print $html;
	include("links.inc.php");
	printinfo();
	return true;
}

function htmladmend()
{
	$html = <<<EOD
</body>
</html>
EOD;
	print $html;
	return true;
}

function validatetoken($token1)
{
	$token2 = $_SESSION['token'];
	if (!compare_tokens($token1, $token2)) {
		print "Data *not* saved! Your token is invalid. Probably just a temporary error. Please <a href=\"../logout\">logout</a> and login again.";
		exit;
	}
}

// Add data
function get_create_person($name, $internal = "Autoimport")
{
	$name = trim($name);
	preg_match('_(.*) (.*)_', $name, $names);
	$person_id = getone("SELECT id FROM person WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($name) . "'");
	if (!$person_id) {
		$sql = "INSERT INTO person (firstname, surname, internal) VALUES ('" . dbesc($names[1]) . "', '" . dbesc($names[2]) . "', '" . dbesc($internal) . "')";
		$person_id = doquery($sql);
		chlog($person_id, 'person', 'Person created');
	}
	return $person_id;
}

function create_game($game, $internal = "Autoimport", $multiple_runs = FALSE, $existing_game_id = FALSE)
{
	$title = $game['title'];
	$gamesystem_id = $game['gamesystem_id'] ?? NULL;
	$gamesystem_extra = $game['gamesystem_extra'] ?? '';
	$urls = $game['urls'] ?? [];
	$genres = $game['genres'] ?? [];
	$tags = $game['tags'] ?? [];
	$persons = $game['persons'] ?? [];
	$gms = $game['gms'] ?? [];
	$cons = $game['cons'] ?? []; // list of con ids, e.g. [1, 4, 6] - assuming premiere
	$organizer = $game['organizer'] ?? '';
	$descriptions = $game['descriptions'] ?? [];
	$players_min = $game['players_min'] ?? NULL;
	$players_max = $game['players_max'] ?? NULL;
	$participants_extra = $game['participants_extra'] ?? '';
	$person_ids = [];
	$gm_ids = [];
	foreach ($persons as $person) {
		if (trim($person['name'])) {
			$person_ids[] = [
				'pid' => get_create_person(trim($person['name']), $internal),
				'role_id' => $person['role_id']
			];
		}
	}

	foreach ($gms as $gm) {
		if (trim($gm['name'])) {
			$gm_ids[] = [
				'pid' => get_create_person(trim($gm['name']), $internal),
				'role_id' => $gm['role_id']
			];
		}
	}

	if ($gamesystem_id == NULL) { // insert text NULL into SQL
		$gamesystem_id = 'NULL';
	}

	// insert game
	$game_id_sql = "INSERT INTO game (title, internal, gamesystem_id, gamesystem_extra, person_extra, players_min, players_max, participants_extra, rlyeh_id, boardgame) " .
		"VALUES ('" . dbesc($title) . "', '" . dbesc($internal) . "', $gamesystem_id, '" . dbesc($gamesystem_extra) . "', '" . dbesc($organizer) . "', " . strNullEscape($players_min) . ", " . strNullEscape($players_max) . ", '" . dbesc($participants_extra) . "', 0, 0)";
	$game_id = doquery($game_id_sql);
	if (!$game_id) {
		return false;
	}
	chlog($game_id, 'game', 'Game created');

	/*
    if ($description) {
        $language = 'sv';
        if ($multiple_runs || $existing_game_id) {
            $language .= " ($year)";
        }
        $desc_sql = "INSERT INTO game_description (game_id, description, language) VALUES ($game_id, '" . dbesc($description) . "', '$language')";
        doquery($desc_sql);
    }

    if ($year) {
        $begin = $end = $year . '-00-00';
        $run_sql = "INSERT INTO gamerun (game_id, begin, end, location, country) VALUES ($game_id, '$begin', '$end', '" . dbesc($location) . "', 'se')";
        doquery($run_sql);        
    }
	*/
	$year = '';

	foreach ($person_ids as $person) {
		$pid = $person['pid'];
		$role_id = $person['role_id'];
		if ($multiple_runs || $existing_game_id) {
			$assql = "INSERT INTO pgrel (person_id, game_id, title_id, note) VALUES ($pid, $game_id, $role_id, '$year run')";
		} else {
			$assql = "INSERT INTO pgrel (person_id, game_id, title_id) VALUES ($pid, $game_id, $role_id)";
		}
		doquery($assql);
	}

	foreach ($gm_ids as $gm) {
		$pid = $gm['pid'];
		$role_id = $gm['role_id'];
		foreach ($cons AS $con_id) {
			$gmsql = "INSERT INTO pgrel (person_id, game_id, title_id, convention_id, note) VALUES ($pid, $game_id, $role_id, $con_id, '')";
			doquery($gmsql);
		}
	}
	
	foreach ($descriptions as $language => $description) {
		$desc_sql = "INSERT INTO game_description (game_id, description, language) VALUES ($game_id, '" . dbesc($description) . "', '$language')";
		doquery($desc_sql);
	}

	foreach ($genres as $gid) {
		if (!getone("SELECT 1 FROM ggrel WHERE genre_id = $gid AND game_id = $game_id")) {
			$gsql = "INSERT INTO ggrel (genre_id, game_id) VALUES ($gid, $game_id)";
			doquery($gsql);
		}
	}

	foreach ($tags as $tag) {
		if ($tag != '' && !getone("SELECT 1 FROM tags WHERE game_id = $game_id AND tag = '" . dbesc($tag) . "'")) {
			$tsql = "INSERT INTO tags (game_id, tag) VALUES ($game_id, '" . dbesc($tag) . "')";
			doquery($tsql);
		}
	}

	foreach ($urls as $url) {
		if ($url != '' && !getone("SELECT 1 FROM links WHERE game_id = $game_id AND url = '" . dbesc($url) . "'")) {
			$lsql = "INSERT INTO links (game_id, url, description) VALUES ($game_id, '" . dbesc($url) . "', '{\$_sce_file_scenario}')";
			doquery($lsql);
		}
	}

	foreach ($cons as $con_id) { // assuming premiere
		$csql = "INSERT INTO cgrel (convention_id, game_id, presentation_id) VALUES ($con_id, $game_id, 1)";
		doquery($csql);
	}

	return $game_id;
}
