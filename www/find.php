<?php
// redirect, hvis resultatet sandsynligvis findes?
$redirect = TRUE;
$rredirect = $_REQUEST['redirect'] ?? '';
if ($rredirect == 'no') $redirect = FALSE;

$debug = FALSE;

require("./connect.php");
require("base.inc.php");
require("smartfind.inc.php");

$cat = $_REQUEST['cat'] ?? '';
$find = $_GET['find'] ?? $_GET['q'] ?? '';
$search_title = (string) ($_REQUEST['search_title'] ?? '');
$search_description = (string) ($_REQUEST['search_description'] ?? '');
$search_system = (int) ($_REQUEST['search_system'] ?? '');
$search_genre = array_unique((array) ($_REQUEST['search_genre'] ?? []));
$search_conset = (int) ($_REQUEST['search_conset'] ?? 0);
$search_download = (string) ($_REQUEST['search_download'] ?? '');
$search_filelanguage = array_unique((array) ($_REQUEST['search_filelanguage'] ?? []));
$search_players = (int) ($_REQUEST['search_players'] ?? 0);
$search_no_gm = (string) ($_REQUEST['search_no_gm'] ?? '');
$search_boardgames = (string) ($_REQUEST['search_boardgames'] ?? '');
$search_tag = (string) ($_REQUEST['tag'] ?? '');
$id_data = [];

// achievements
function check_search_achievements($find)
{
  if (!$find) return false;
  if (strtolower($find) == strrev(strtolower($find)) && strlen($find) > 1) award_achievement(48); // palindrome
  if ((strpos(strtolower($find), 'drop table')) !== FALSE) award_achievement(44); // sql injection
}

function search_articles($find)
{
  global $t;
  $sql = "
		SELECT i.id AS issueid, m.name AS magazinename, i.title AS issuetitle, a.title, a.description, a.page
		FROM article a
		INNER JOIN issue i ON a.issue_id = i.id
		INNER JOIN magazine m ON i.magazine_id = m.id 
		WHERE a.title LIKE '%" . likeesc($find) . "%'
		OR a.description LIKE '%" . likeesc($find) . "%'
		ORDER BY m.name, i.releasedate, i.title, a.title, i.id
	";
  $articles = getall($sql);
  if (!$articles) return false;
  $output = "<ul>" . PHP_EOL;
  foreach ($articles as $article) {
    $output .= "<li>" .
      getdatahtml('issue', $article['issueid'], getentry('issue', $article['issueid'], FALSE, TRUE)) .
      "<ul><li>" . preg_replace('/' . preg_quote($find, '/') . '/i', '<b>$0</b>', textlinks(htmlspecialchars($article['title'] . ($article['description'] ? ' - ' . $article['description'] : '')))) .
      ($article['page'] ? " (" . $t->getTemplateVars('_file_page') . " " . htmlspecialchars($article['page']) . ')' : '') .
      '</li></ul>' .
      '</li>' . PHP_EOL;
  }
  $output .= "</ul>" . PHP_EOL;
  return $output;
}

function search_files($find, $category = '')
{
  global $t;
  $data_field = getFieldFromCategory($category);
  $where_category = ($category ? "AND a.$data_field IS NOT NULL" : "");
  $preview_length = 30;
  $output = "";

  $sql = "
		SELECT a.id, COALESCE(game_id, convention_id, conset_id, gamesystem_id, tag_id, issue_id) AS data_id, CASE WHEN !ISNULL(game_id) THEN 'game' WHEN !ISNULL(convention_id) THEN 'convention' WHEN !ISNULL(conset_id) THEN 'conset' WHEN !ISNULL(gamesystem_id) THEN 'gamesystem' WHEN !ISNULL(tag_id) THEN 'tag' WHEN !ISNULL(issue_id) THEN 'issue' END AS category, a.description, a.language, b.label, b.archivefile, GROUP_CONCAT(b.label ORDER BY b.id SEPARATOR ', ') AS page, SUBSTRING(b.content, LOCATE('" . dbesc($find) . "',content)-" . $preview_length . ", LENGTH('" . dbesc($find) . "')+" . ($preview_length * 2) . ") AS preview, b.content
		FROM files a
		INNER JOIN filedata b ON a.id = b.files_id
		WHERE MATCH(content) AGAINST ('\"" . dbesc($find) . "\"' IN BOOLEAN MODE)
		$where_category
		GROUP BY a.id, b.archivefile
		ORDER BY category, data_id, a.id, b.archivefile
	";
  $result = getall($sql);
  if (!$result) return false;
  $last_id = 0;
  $output = "<ul>";
  foreach ($result as $row) {
    $page = (strlen($row['page']) > 50 ? substr($row['page'], 0, 50) . "..." : $row['page']);
    if ($last_id != $row['data_id']) {
      if ($last_id != 0) {
        $output .= "</ul></li>";
      }
      $output .= "<li>" .
        getdatahtml($row['category'], $row['data_id'], getentry($row['category'], $row['data_id'], FALSE, ($row['category'] == 'issue'))) .
        "<ul>";
      $last_id = $row['data_id'];
    }
    $languagetext = "";
    if ($row['language']) {
      $languages = explode(",", $row['language']);
      $fulllanguages = [];
      foreach ($languages as $language) {
        $fulllanguages[] = getLanguageName($language);
      }
      $languagetext .= " [" . implode(", ", $fulllanguages) . "]";
    }
    $archivefiletext = "";
    if ($row['archivefile']) {
      // $archivefiletext = htmlspecialchars(" (" . str_replace('/',' / ', $row['archivefile']) . ")" );
      $archivefiletext = htmlspecialchars(" (" . basename($row['archivefile']) . ")"); // might skip first char if special char and setlocale() is not active
    }
    $output .= "<li>" .
      htmlspecialchars(parseTemplate($row['description'])) .
      $languagetext .
      $archivefiletext .
      " (" . $t->getTemplateVars('_file_page') . " " . htmlspecialchars($page) . ")";
    if ((stripos($row['content'], $find)) !== FALSE) {
      $output .= "<br />" .
        "&nbsp;&nbsp;.. " . preg_replace('/^.*?\s(.{0,40})(' . preg_quote($find, '/') . ')(.{0,40})\s.*$/si', '$1<span class="highlightsearch">$2</span>$3', htmlspecialchars($row['content'])) . " ..";
    }
    $output .= "</li>";
  }
  $output .= "</ul></li>";
  $output .= "</ul>";
  return $output;
}

function search_blogposts($find)
{
  global $t;
  $preview_length = 30;
  $output = "";

  $sql = "
		SELECT a.id, a.feed_id, a.title, a.link, a.pubdate, a.content, SUBSTRING(a.content, LOCATE('" . dbesc($find) . "',content)-" . $preview_length . ", LENGTH('" . dbesc($find) . "')+" . ($preview_length * 2) . ") AS preview, b.owner, b.name
		FROM feedcontent a
		INNER JOIN feeds b ON a.feed_id = b.id
		WHERE a.content LIKE '%" . likeesc($find) . "%'
		ORDER BY a.pubdate DESC
	";
  $result = getall($sql);
  if (!$result) return false;
  $output = "<ul>";
  foreach ($result as $row) {

    $output .= "<li><a href=\"" . $row['link'] . "\">" . htmlspecialchars($row['title']) . "</a> (" . fulldate(date("Y-m-d", strtotime($row['pubdate']))) . ")";
    $output .= "<ul><li>";
    $output .= sprintf($t->getTemplateVars('_find_blogposthit'), '<i>' . htmlspecialchars($row['name']) . '</i>', htmlspecialchars($row['owner']));
    if ((stripos($row['content'], $find)) !== FALSE) {
      $output .= "<br />" .
        "&nbsp;&nbsp;.. " . preg_replace('/^.*?\s(.{0,40})(' . preg_quote($find, '/') . ')(.{0,40})\s.*$/si', '$1<span class="highlightsearch">$2</span>$3', htmlspecialchars($row['content'])) . " ..";
    }
    $output .= "</li>";
    $output .= "</ul></li>";
  }
  $output .= "</ul>";
  return $output;
}

function search_tags($find)
{
  $sql = "
		(SELECT tag FROM tag WHERE tag LIKE '%" . likeesc($find) . "%')
		UNION
		(SELECT DISTINCT tag FROM tags WHERE tag LIKE '%" . likeesc($find) . "%' ORDER BY tag)
	";
  $result = getall($sql);
  if (!$result) return false;
  $output = "<ul>";
  foreach ($result as $row) {
    $output .= "<li><a href=\"data?tag=" . rawurlencode($row['tag']) . "\" class=\"tag\">" . htmlspecialchars($row['tag']) . "</a>";
    $output .= "</li>";
  }
  $output .= "</ul>";
  return $output;
}

function display_result($match, $linkpart, $class, $short)
{
  $html = "";
  $list = [];
  global $id_data;
  if ($match) {
    $html .= "<ul class=\"indatalist\">\n";

    // Collect and sort data
    foreach ($match as $m_id) {
      $list[$m_id] = $id_data[$short][$m_id] ?? '';
    }
    asort($list);
    foreach ($list as $key => $value) {
      if ($linkpart == 'magazine') {
        $link = 'magazines?id=' . $key;
      } elseif ($linkpart == 'location') {
        $link = 'locations?id=' . $key;
      } else {
        $link = 'data?' . $linkpart . '=' . $key;
      }
      $html .= "<li><a href=\"$link\" class=\"$class\">" . htmlspecialchars($value) . "</a></li>\n";
    }
    $html .= "</ul>\n";
  }
  return $html;
}


// Achievements?
check_search_achievements($find);

// Some quick find code:

if ($find) {
  if (preg_match("/^([cspfgrmil]|cs)(\d+)$/i", $find, $regs)) {
    $pref = strtolower($regs[1]);
    $id = $regs[2];

    switch ($pref) {
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

      case "m":
        header("Location: magazines?id=$id");
        exit;
        break;

      case "i":
        header("Location: magazines?issue=$id");
        exit;
        break;

      case "l":
        header("Location: locations?id=$id");
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

  $match = $link_a = $link_b = [];
  $id_data = [];

  if (!$cat || $cat == "person") {
    category_search($find, "CONCAT(firstname,' ',surname)", "person");
  }

  if (!$cat || $cat == "game") {
    category_search($find, "title", "game");
  }

  if (!$cat || $cat == "con") {
    category_search($find, "CONCAT(name, ' (', year, ')') ", "convention");
    category_search($find, "name", "conventionwithyear");
  }

  if (!$cat || $cat == "sys") {
    category_search($find, "name", "gamesystem");
  }

  if (!$cat || $cat == "magazine") {
    category_search($find, "name", "magazine");
  }

  if (!$cat || $cat == "locations") {
    category_search($find, "name", "locations");
  }

  // If only one perfect match, redirect user at once
  if ($redirect == TRUE) {
    if (count($link_a) == 1) {
      $link = array_shift($link_a);
      log_search($find, $link);
      $location_header = 'Location: ' . $link . '&searchterm=' . rawurlencode($find);
      header($location_header);
      exit;
    } elseif (count($link_b) == 1 && strlen($find) >= 4) {
      $link = array_shift($link_b);
      award_achievement(59); // find result with bad spelling
      log_search($find, $link);
      $location_header = 'Location: ' . $link . '&searchterm=' . rawurlencode($find);
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
    category_search($search_title, "title", "game");
  } else { // set titles
    $id_data = [];
    foreach (getall("SELECT id, title FROM game") as $row) {
      $id_data['game'][$row['id']] = $row['title'];
    }
  }

  if (!$search_title && !$search_description && !$search_system && !$search_genre && !$search_conset && !$search_download && !$search_filelanguage && !$search_players && !$search_no_gm && !$search_boardgames) { // searched for nothing - blank results
    $match['game'] = [];
  } elseif ($search_title && !($match['game'])) { // title searched, but no match
    $match['game'] = [];
  } else {
    if ($match['game'] ?? FALSE) { // found specific titles
      $where[] = "id IN (" . implode(",", $match['game']) . ")";
    }
    if ($search_system) {
      $where[] = "gamesystem_id = '" . (int)$search_system . "'";
    }
    if ($search_players) {
      $where[] = "players_min <= " . $search_players . " AND players_max >= " . $search_players;
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
    $q = "SELECT id FROM game";
    if ($where) $q .= " WHERE " . implode(" AND ", $where);
    $match['game'] = getcol($q);

    // search found, check for description
    if ($search_description && $match['game']) {
      $q = "
				SELECT game.id
				FROM game
				INNER JOIN game_description ON game.id = game_description.game_id
				WHERE game_description.description LIKE '%" . likeesc($search_description) . "%'
				AND game.id IN (" . implode(",", $match['game']) . ")
				GROUP BY game.id
			";
      $match['game'] = getcol($q);
    }

    // search found, check for conset
    if ($search_conset && $match['game']) {
      $q = "
				SELECT game.id
				FROM game, cgrel, convention
				WHERE game.id = cgrel.game_id
				AND cgrel.convention_id = convention.id
				AND convention.conset_id = '$search_conset'
				AND game.id IN (" . implode(",", $match['game']) . ")
				GROUP BY game.id
			";
      $match['game'] = getcol($q);
    }

    // search found, check for genres
    if ($search_genre && $match['game']) {
      $num_genre = count($search_genre);
      $q = "
				SELECT game.id
				FROM game, ggrel
				WHERE game.id = ggrel.game_id
				AND ggrel.genre_Id IN ('" . implode("','", $search_genre) . "')
				AND game.id IN (" . implode(",", $match['game']) . ")
				GROUP BY game.id
				HAVING COUNT(*) = $num_genre
			";
      $match['game'] = getcol($q);
    }

    // search found, check for download
    if (($search_download || $search_filelanguage) && $match['game']) {
      $q = "
				SELECT DISTINCT game_id
				FROM files
				WHERE downloadable = 1
				AND game_id IN (" . implode(",", $match['game']) . ")
			";
      if ($search_filelanguage) {
        $languages = [];
        foreach ($search_filelanguage as $language) {
          $languages[] = '"' . $language . '"';
        }
        $q .= " AND language IN (" . implode(',', $languages) . ")";
      }
      $match['game'] = getcol($q);
    }
  }
} elseif ($search_tag) {
  $q = "
		SELECT DISTINCT game_id
		FROM tags
		WHERE tag = '" . dbesc($search_tag) . "'
	";
  $match['game'] = getcol($q);

  $id_data = [];
  foreach (
    getall("
			SELECT game.id, game.title FROM game INNER JOIN tags ON game.id = tags.game_id
			WHERE tag = '" . dbesc($search_tag) . "'
		") as $row
  ) {
    $id_data['game'][$row['id']] = $row['title'];
  }
}

$out = "";

if ($debug) {
  print "<h2>Class 1 links:</h2>" . implode("<br>", $link_a);
  print "<h2>Class 2 links:</h2>" . implode("<br>", $link_b);
}

// Smarty
$t->assign('find_person', display_result($match['person'] ?? FALSE, "person", "person", "person"));
$t->assign('find_game', display_result($match['game'] ?? FALSE, "scenarie", "scenarie", "game"));
$t->assign('find_convention', display_result($match['convention'] ?? FALSE, "con", "con", "convention"));
$t->assign('find_gamesystem', display_result($match['gamesystem'] ?? FALSE, "system", "system", "gamesystem"));
$t->assign('find_magazines', display_result($match['magazine'] ?? FALSE, "magazine", "magazine", "magazine"));
$t->assign('find_locations', display_result($match['locations'] ?? FALSE, "location", "location", "locations"));
$t->assign('find_tags', $tagsearch ?? "");
$t->assign('find_files', $filesearch ?? "");
$t->assign('find_articles', $articlesearch ?? "");
$t->assign('find_blogposts', $blogsearch ?? "");
$t->assign('search_boardgames', $search_boardgames);
$t->display('find.tpl');
exit;
