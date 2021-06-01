<?php
// redirect, hvis resultatet sandsynligvis findes?
$redirect = TRUE;
$rredirect = $_REQUEST['redirect'] ?? '';
if ($rredirect == 'no') $redirect = FALSE;

$debug = FALSE;

require("./connect.php");
require("base.inc.php");
require("smartfind.inc.php");

$find = "";
$cat = $_REQUEST['cat'] ?? '';
$find = $_GET['find'] ?? $_GET['q'] ?? '';
$search_title = (string) ( $_REQUEST['search_title'] ?? '');
$search_description = (string) ( $_REQUEST['search_description'] ?? '');
$search_system = (int) ( $_REQUEST['search_system'] ?? '');
$search_genre = array_unique((array) ( $_REQUEST['search_genre'] ?? []) );
$search_conset = (int) ( $_REQUEST['search_conset'] ?? 0);
$search_download = (string) ( $_REQUEST['search_download'] ?? '');
$search_filelanguage = array_unique((array) ( $_REQUEST['search_filelanguage'] ?? []) );
$search_players = (int) ( $_REQUEST['search_players'] ?? 0);
$search_no_gm = (string) ( $_REQUEST['search_no_gm'] ?? '');
$search_boardgames = (string) ( $_REQUEST['search_boardgames'] ?? '');
$search_tag = (string) ( $_REQUEST['tag'] ?? '');

// achievements
function check_search_achievements ($find) {
	if (!$find) return false;
	if (strtolower($find) == strrev(strtolower($find)) && strlen($find) > 1) award_achievement(48); // palindrome
	if ((strpos(strtolower($find), 'drop table')) !== FALSE) award_achievement(44); // sql injection
}

function search_articles($find) {
	global $t;
	$sql = "
		SELECT i.id AS issueid, m.name AS magazinename, i.title AS issuetitle, a.title, a.description, a.page
		FROM article a
		INNER JOIN issue i ON a.issue_id = i.id
		INNER JOIN magazine m ON i.magazine_id = m.id 
		WHERE a.title LIKE '%".dbesc($find)."%'
		OR a.description LIKE '%".dbesc($find)."%'
		ORDER BY m.name, i.title, a.title, i.id
	";
	$articles = getall($sql);
	if (!$articles) return false;
	$output = "<ul>";
	foreach ($articles AS $article) {
		$output .= "<li>" .
		getdatahtml('issue',$article['issueid'],getentry('issue',$article['issueid'], FALSE, TRUE ) ) .
		"<ul><li>" . textlinks(htmlspecialchars($article['title'] . ($article['description'] ? ' - ' . $article['description'] : '') ) ) . " (" . $t->getTemplateVars('_file_page') . " " . htmlspecialchars($article['page']) . ')</li></ul>';
		"</li>";
	}
	$output .= "</ul>";
	return $output;
}

function search_files ($find, $category = '') {
	global $t;
	$where_category = ($category ? "AND a.category = '$category'" : "");
	$preview_length = 30;
	$output = "";

	$sql = "
		SELECT a.id, a.category, a.data_id, a.description, a.language, b.label, b.archivefile, GROUP_CONCAT(b.label ORDER BY b.id SEPARATOR ', ') AS page, SUBSTRING(b.content, LOCATE('".dbesc($find)."',content)-".$preview_length.", LENGTH('".dbesc($find)."')+".($preview_length*2).") AS preview, b.content
		FROM files a
		INNER JOIN filedata b ON a.id = b.files_id
		WHERE MATCH(content) AGAINST ('\"".dbesc($find)."\"' IN BOOLEAN MODE)
		$where_category
		GROUP BY a.id, b.archivefile
		ORDER BY a.category, a.data_id, a.id, b.archivefile
	";
	$result = getall($sql);
	if (!$result) return false;
	$last_id = 0;
	$output = "<ul>";
	foreach($result AS $row) {
		$page = (strlen($row['page']) > 50 ? substr($row['page'],0,50)."..." : $row['page']);
		if ($last_id != $row['data_id']) {
			if ($last_id != 0) {
				$output .= "</ul></li>";
			}
			$output .= "<li>".
			           getdatahtml($row['category'],$row['data_id'],getentry($row['category'],$row['data_id'], FALSE, ($row['category'] == 'issue') ) ).
			           "<ul>";
			$last_id = $row['data_id'];
		}
		$languagetext = "";
		if ( $row['language'] ) {
			$languages = explode( ",", $row['language']);
			$fulllanguages = [];
			foreach( $languages AS $language ) {
				$fulllanguages[] = getLanguageName( $language );
			}
			$languagetext .= " [" . implode( ", ", $fulllanguages) . "]";
		}
		$archivefiletext = "";
		if ($row['archivefile']) {
			// $archivefiletext = htmlspecialchars(" (" . str_replace('/',' / ', $row['archivefile']) . ")" );
			$archivefiletext = htmlspecialchars(" (" . basename($row['archivefile']) . ")" ); // might skip first char if special char and setlocale() is not active
		}
		$output .= "<li>".
				   htmlspecialchars(parseTemplate($row['description'])) .
				   $languagetext .
				   $archivefiletext .
		           " (" . $t->getTemplateVars('_file_page') . " " . htmlspecialchars($page) . ")";
		if ((stripos($row['content'],$find)) !== FALSE) {
			$output .= "<br />".
			           "&nbsp;&nbsp;.. ".preg_replace('/^.*?\s(.{0,40})('.preg_quote($find,'/').')(.{0,40})\s.*$/si','$1<span class="highlightsearch">$2</span>$3',htmlspecialchars($row['content']))." ..";
		}
		$output .= "</li>";	
	}
	$output .= "</ul></li>";
	$output .= "</ul>";
	return $output;
}

function search_blogposts ($find) {
	global $t;
	$preview_length = 30;
	$output = "";

	$sql = "
		SELECT a.id, a.feed_id, a.title, a.link, a.pubdate, a.content, SUBSTRING(a.content, LOCATE('".dbesc($find)."',content)-".$preview_length.", LENGTH('".dbesc($find)."')+".($preview_length*2).") AS preview, b.owner, b.name
		FROM feedcontent a
		INNER JOIN feeds b ON a.feed_id = b.id
		WHERE a.content LIKE '%".dbesc($find)."%'
		ORDER BY a.pubdate DESC
	";
	$result = getall($sql);
	if (!$result) return false;
	$output = "<ul>";
	foreach($result AS $row) {
		
		$output .= "<li><a href=\"".$row['link']."\">".htmlspecialchars($row['title'])."</a> (" . fulldate( date("Y-m-d", strtotime($row['pubdate']) ) ) . ")";
		$output .= "<ul><li>";
		$output .= sprintf( $t->getTemplateVars('_find_blogposthit'), '<i>' . htmlspecialchars($row['name']) . '</i>', htmlspecialchars($row['owner']) );
		if ((stripos($row['content'],$find)) !== FALSE) {
			$output .= "<br />".
			           "&nbsp;&nbsp;.. ".preg_replace('/^.*?\s(.{0,40})('.preg_quote($find,'/').')(.{0,40})\s.*$/si','$1<span class="highlightsearch">$2</span>$3',htmlspecialchars($row['content']))." ..";
		}
		$output .= "</li>";
		$output .= "</ul></li>";
	}
	$output .= "</ul>";
	return $output;
}

function search_tags ($find) {
	$sql = "
		(SELECT tag FROM tag WHERE tag LIKE '%" . dbesc($find) . "%')
		UNION
		(SELECT DISTINCT tag FROM tags WHERE tag LIKE '%" . dbesc($find) . "%' ORDER BY tag)
	";
	$result = getall($sql);
	if (!$result) return false;
	$output = "<ul>";
	foreach($result AS $row) {
		$output .= "<li><a href=\"data?tag=" . rawurlencode($row['tag']) . "\" class=\"tag\">" . htmlspecialchars($row['tag']) . "</a>";
		$output .= "</li>";
	}
	$output .= "</ul>";
	return $output;
}


function display_result ($match,$linkpart,$class,$short) {
	$html = "";
	global $id_data;
	if ($match) {
		$html .= "<ul class=\"indatalist\">\n";

// samler data sammen og sorterer alfabetisk
		foreach($match AS $m_id) {
			$list[$m_id] = $id_data[$short][$m_id];
		}
		asort($list);
		foreach($list AS $key => $value) {
			$link = ($linkpart != 'magazine' ? "data?$linkpart=$key" : "magazines?id=$key");
			$html .= "<li><a href=\"$link\" class=\"$class\">".htmlspecialchars($value)."</a></li>\n";
		}
		$html .= "</ul>\n";
	}
	return $html;

}


// Achievements?
check_search_achievements($find);

// Some quick find code:

if ($find) {
	if (preg_match("/^([cspfgr]|cs)(\d+)$/i",$find,$regs)) {
		$pref = strtolower($regs[1]);
		$id = $regs[2];
	
		switch($pref) {
			case "s":
			case "g":
				header("Location: data?scenarie=$id");
				exit;
				break;
			
			case "c":
				header("Location: data?con=$id");
				exit;
				break;
	
			case "p":
			case "f":
				header("Location: data?person=$id");
				exit;
				break;

			case "cs":
				header("Location: data?conset=$id");
				exit;
				break;

			case "r":
				header("Location: data?review=$id");
				exit;
				break;
	
		}
	}

// Begin wild search
//
// $link_a are links for perfect matches
// $link_b are links for good matches
// $match[kategori] are id's for any type of match
// (in theory :)

	$match = $link_a = $link_b = array();
	$id_data = array();

	if (!$cat || $cat == "aut" ) {
		category_search($find, "CONCAT(firstname,' ',surname)", "aut");
	}

	if (!$cat || $cat == "sce" ) {
		category_search($find, "title", "sce");
	}

	if (!$cat || $cat == "con" ) {
		category_search($find, "CONCAT(name, ' (', year, ')') ", "convent");
	}

	if (!$cat || $cat == "sys" ) {
		category_search($find, "name", "sys");
	}

	if (!$cat || $cat == "magazine" ) {
		category_search($find, "name", "magazine");
	}


// If only one perfect match, redirect user at once
	if ($redirect == TRUE) {
		if (count($link_a) == 1) {
			$link = array_shift($link_a);
			log_search($find,$link);
			$location_header = "Location: $link";
			header($location_header);
			exit;
		} elseif (count($link_b) == 1 && strlen($find) >= 4) {
			$link = array_shift($link_b);
			award_achievement(59); // find result with bad spelling
			log_search($find,$link);
			$location_header = "Location: $link";
			header($location_header);
			exit;
		}
	}

	$tagsearch = search_tags($find);
	$filesearch = search_files($find);
	$blogsearch = search_blogposts($find);
	$articlesearch = search_articles($find);
	log_search($find);
	
} elseif ($_REQUEST['search_type'] == 'findspec') {
	$where = [];
	if ($search_title) { // pre-search for titles
		category_search($search_title, "title", "sce");
	} else { // set titles
		$id_data = array();
		foreach(getall("SELECT id, title FROM sce") AS $row) {
			$id_data['sce'][$row['id']] = $row['title'];
		}
	}
	
	if (!$search_title && !$search_description && !$search_system && !$search_genre && !$search_conset && !$search_download && ! $search_filelanguage && !$search_players && !$search_no_gm && !$search_boardgames) { // searched for nothing - blank results
		$match['sce'] = [];
	} elseif ($search_title && !($match['sce']) ) { // title searched, but no match
		$match['sce'] = [];
	} else {
		if ($match['sce']) { // found specific titles
			$where[] = "id IN (".implode(",",$match['sce']).")";
		}
		if ($search_system) {
			$where[] = "sys_id = '".(int)$search_system."'";
		}
		if ($search_players) {
			$where[] = "players_min <= " . $search_players ." AND players_max >= " . $search_players;
		}
		if ($search_no_gm) {
			$where[] = 'gms_min = 0';
			if (!$search_boardgames) {
				$where[] = 'boardgame = 0';
			}
		}
		if ($search_boardgames) {
			$where[] = "boardgame = 1";
		}
		$q = "SELECT id FROM sce";
		if ($where) $q .= " WHERE ".implode(" AND ",$where);
		$match['sce'] = getcol($q);

		// search found, check for description
		if ($search_description && $match['sce']) {
			$q = "
				SELECT sce.id
				FROM sce
				INNER JOIN game_description ON sce.id = game_description.game_id
				WHERE game_description.description LIKE '%".dbesc($search_description)."%'
				AND sce.id IN (".implode(",",$match['sce']).")
				GROUP BY sce.id
			";
			$match['sce'] = getcol($q);
		}

		// search found, check for conset
		if ($search_conset && $match['sce']) {
			$q = "
				SELECT sce.id
				FROM sce, csrel, convent
				WHERE sce.id = csrel.sce_id
				AND csrel.convent_id = convent.id
				AND convent.conset_id = '$search_conset'
				AND sce.id IN (".implode(",",$match['sce']).")
				GROUP BY sce.id
			";
			$match['sce'] = getcol($q);
		}

		// search found, check for genres
		if ($search_genre && $match['sce']) {
			$num_genre = count($search_genre);
			$q = "
				SELECT sce.id
				FROM sce, gsrel
				WHERE sce.id = gsrel.sce_id
				AND gsrel.gen_id IN ('".implode("','",$search_genre)."')
				AND sce.id IN (".implode(",",$match['sce']).")
				GROUP BY sce.id
				HAVING COUNT(*) = $num_genre
			";
			$match['sce'] = getcol($q);
		}

		// search found, check for download
		if ( ( $search_download || $search_filelanguage ) && $match['sce'] ) {
			$q = "
				SELECT DISTINCT data_id
				FROM files
				WHERE category = 'sce'
				AND downloadable = 1
				AND data_id IN (".implode(",",$match['sce']).")
			";
			if ( $search_filelanguage ) {
				$languages = [];
				foreach ( $search_filelanguage AS $language ) {
					$languages[] = '"' . $language . '"';
				}
				$q .= " AND language IN (" . implode(',', $languages) . ")";
			}

			$match['sce'] = getcol($q);
		}

	}

} elseif ($search_tag) {
	$q = "
		SELECT DISTINCT sce_id
		FROM tags
		WHERE tag = '" . dbesc($search_tag) . "'
	";
	$match['sce'] = getcol($q);

	$id_data = [];
	foreach(getall("
			SELECT sce.id, sce.title FROM sce INNER JOIN tags ON sce.id = tags.sce_id
			WHERE tag = '" . dbesc($search_tag) . "'
		") AS $row) {
		$id_data['sce'][$row['id']] = $row['title'];
	}
}

$out = "";

if ($debug) {
	print "<h2>Klasse 1-links:</h2>".implode("<br>",$link_a);
	print "<h2>Klasse 2-links:</h2>".implode("<br>",$link_b);
	print "<h2>Alle links:</h2>".implode("<br>",$match['aut']);
}

// Smarty
$t->assign('find_aut', display_result($match['aut'], "person", "person", "aut") );
$t->assign('find_sce', display_result($match['sce'], "scenarie", "scenarie", "sce") );
$t->assign('find_convent', display_result($match['convent'], "con", "con", "convent") );
$t->assign('find_sys', display_result($match['sys'], "system", "system", "sys") );
$t->assign('find_magazines', display_result($match['magazine'], "magazine", "magazine", "magazine") );
$t->assign('find_tags', $tagsearch ?? "" );
$t->assign('find_files', $filesearch ?? "" );
$t->assign('find_articles', $articlesearch ?? "" );
$t->assign('find_blogposts', $blogsearch ?? "" );
$t->assign('search_boardgames', $search_boardgames );
$t->display('find.tpl');
exit;

?>
