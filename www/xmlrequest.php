<?php
require("./connect.php");
require("./base.inc.php");
$output = "";

$action = (string) ($_REQUEST['action'] ?? '');
$q = (string) ($_REQUEST['q'] ?? '');
$term = (string) ($_REQUEST['term'] ?? '');

$likesearch = likeesc((string) ($_REQUEST['q'] ?? ''));

if ($action == "lookup") {
  if ($q) {
    $query = "
			SELECT id, CONCAT(firstname,' ',surname) AS label, 'person' AS type, 'person' AS linkpart FROM person WHERE CONCAT(firstname,' ',surname) LIKE '$likesearch%'
			UNION ALL
			SELECT id, CONCAT(surname,', ',firstname) AS label, 'person' AS type, 'person' AS linkpart FROM person WHERE CONCAT(surname,', ',firstname) LIKE '$likesearch%'
			UNION ALL
			SELECT id, title AS label, 'game' AS type, 'scenarie' AS linkpart FROM person WHERE title LIKE '$likesearch%'
			UNION ALL
			SELECT id, name AS label, 'gamesystem' AS type, 'system' AS linkpart FROM gamesystem WHERE name LIKE '$likesearch%'
			UNION ALL
			SELECT id, CONCAT(name,' (',year,')') AS label, 'convention' AS type, 'con' AS linkpart FROM convention WHERE name LIKE '$likesearch%' OR CONCAT(name,' (',year,')') LIKE '$likesearch%' OR CONCAT(name,' ',year) LIKE '$likesearch%'
			ORDER BY label
			
		";
    $result = mysqli_query($dblink, $query) or die(mysqli_error($dblink));
    $i = 0;
    while ($row = mysqli_fetch_row($result)) {
      print "<div class=\"result\">" . getdatahtml($row[2], $row[0], $row[1]) . "</div>\n";
      $i++;
      if ($i > 10) {
        print "<div class=\"result\">...</div>\n";
        break;
      }
    }
  }
  exit;
} elseif ($action == "titlesearch" && $q) {
  include("smartfind.inc.php");
  $match = [];
  $id_data = [];
  $result = [];
  // lots of global variables...
  category_search($q, "title", "game");
  foreach ($match['game'] as $game_id) { // array of arrays to preserve order
    $result[] = [(int) $game_id, $id_data['game'][$game_id]];
  }
  $output = $result;
} elseif ($action == "locationsearch" && $term) {
  $escapequery = dbesc($term);
  $likeescapequery = likeesc($term);
  $refs = getall("
		SELECT l.id, l.name, l.city, l.country
		FROM locations l
		WHERE l.name LIKE '$likeescapequery%'
		OR l.city LIKE '$likeescapequery%'
		OR l.note LIKE '$likeescapequery%'
		OR l.id = '$escapequery'
	");
  $result = [];
  foreach ($refs as $ref) {
    $label = $ref['id'] . ' - ' . $ref['name'];
    if ($ref['city']) {
      $label .= ', ' . $ref['city'];
    }
    if ($ref['country']) {
      $label .= ', ' . getCountryName($ref['country']);
    }
    $result[] = $label;
  }
  $output = $result;
} elseif ($action == "adduserlog" && $_SESSION['user_id'] && $_REQUEST['data_id'] && $_REQUEST['category'] && $_REQUEST['type']) {
  $token = $_REQUEST['token'];
  if (compare_tokens($token, $_SESSION['token'])) {
    adduserlog($_SESSION['user_id'], $_REQUEST['category'], $_REQUEST['data_id'], $_REQUEST['type']);
    $newlabel = $t->getTemplateVars('_top_' . $_REQUEST['type'] . '_pt') ?? 'Done';
    $output = ['newlabel' => $newlabel, 'newdirection' => 'remove', 'switch' => $t->getTemplateVars('_switch')];

    // achievements
    if ($_REQUEST['category'] == 'game') {
      list($gamesystem_id, $boardgame) = getrow("SELECT gamesystem_id, boardgame FROM game WHERE id = " . (int) $_REQUEST['data_id']);
      $fanboy_count = getone("SELECT 1 FROM userlog INNER JOIN pgrel ON userlog.game_id = pgrel.game_id AND pgrel.title_id = 1 INNER JOIN users ON userlog.user_id = users.id WHERE userlog.game_id IS NOT NULL AND userlog.type = 'played' AND users.person_id != pgrel.person_id AND user_id = " . $_SESSION['user_id'] . " GROUP BY pgrel.person_id, userlog.user_id HAVING COUNT(*) >= 10"); // played at least 10 scenario from another author
      $polandsce = getcol("SELECT DISTINCT game_id FROM gamerun WHERE country = 'pl'");
      if ($_REQUEST['type'] == 'read') {
        award_achievement(3);
      }
      if ($_REQUEST['type'] == 'played') {
        award_achievement(4);
      }
      if ($_REQUEST['type'] == 'gmed') {
        award_achievement(5);
      }
      if ($boardgame == 1) {
        award_achievement(87); // board game
      }
      if ($gamesystem_id == 99) { // System: Hinterlandet
        award_achievement(88); // play, read or GM Hinterlandet
      }
      if ($fanboy_count) {
        award_achievement(89); // played at least 10 scenarios written by the same author
      }
      if (in_array($_REQUEST['data_id'], $polandsce)) {
        award_achievement(95); // attend scenario in Poland
      }
    } elseif ($_REQUEST['category'] == 'convention') {
      $future = getone("SELECT begin > NOW() + INTERVAL 7 DAY AS future FROM convention WHERE id = " . (int) $_REQUEST['data_id']);
      if ($future) {
        award_achievement(102); // attend a convention in the future (+ 7 days due to organizer setup)
      }
    }
  } else {
    $output = compare_token_error($token, $_SESSION['token']);
  }
} elseif ($action == "removeuserlog" && $_SESSION['user_id'] && $_REQUEST['data_id'] && $_REQUEST['category'] && $_REQUEST['type']) {
  $token = $_REQUEST['token'];
  if (compare_tokens($token, $_SESSION['token'])) {
    removeuserlog($_SESSION['user_id'], $_REQUEST['category'], $_REQUEST['data_id'], $_REQUEST['type']);
    $newlabel = $t->getTemplateVars('_top_not_' . $_REQUEST['type'] . '_pt') ?? 'Done';
    $output = ['newlabel' => $newlabel, 'newdirection' => 'add', 'switch' => $t->getTemplateVars('_switch')];
  } else {
    $output = compare_token_error($token, $_SESSION['token']);
  }
} elseif ($action == "getlocations") {
  $locations = getall("
		SELECT l.id, l.name, l.address, l.city, l.country, l.note, geo IS NOT NULL AS hasGeo, ST_X(geo) AS latitude, ST_Y(geo) AS longitude, IF(lrel.gamerun_id IS NULL, 'convention', 'gamerun') AS type, IF(lrel.gamerun_id IS NULL, lrel.convention_id, gr.game_id) AS data_id, c.conset_id AS conset_id, IF(lrel.gamerun_id IS NULL, CONCAT(c.name, ' (', IF(c.year, c.year, '?'), ')'), CONCAT(g.title, ' (', IF(YEAR(gr.begin), YEAR(gr.begin), '?'), ')') ) AS data_label, IF(lrel.gamerun_id IS NULL, c.begin, gr.begin) AS data_begin, IF(lrel.gamerun_id IS NULL, c.cancelled, gr.cancelled) AS data_cancelled, COALESCE(c.begin, c.year, gr.begin) AS data_starttime, COALESCE(c.end, c.year, gr.end) AS data_endtime
		FROM locations l
		LEFT JOIN lrel ON l.id = lrel.location_id
		LEFT JOIN convention c ON lrel.convention_id = c.id
		LEFT JOIN gamerun gr ON lrel.gamerun_id = gr.id
		LEFT JOIN game g ON gr.game_id = g.id
		WHERE geo IS NOT NULL
		ORDER BY data_starttime
	", FALSE);

  $events = [];
  foreach ($locations as $event) {
    $location_id = $event['id'];
    if (!isset($events[$location_id])) {
      $events[$location_id] = [
        'data' => ['name' => $event['name'], 'address' => $event['address'], 'city' => $event['city'], 'country' => getCountryName($event['country']), 'note' => $event['note'], 'hasGeo' => $event['hasGeo'], 'latitude' => $event['latitude'], 'longitude' => $event['longitude']],
        'events' => []
      ];
    }
    if ($event['data_id']) {
      $events[$location_id]['events'][] = ['type' => $event['type'], 'data_id' => $event['data_id'], 'data_label' => $event['data_label'], 'data_begin' => $event['data_begin'], 'data_starttime' => $event['data_starttime'], 'data_cancelled' => $event['data_cancelled'], 'conset_id' => $event['conset_id'], 'nicedateset' => nicedateset($event['data_starttime'], $event['data_endtime'])];
    }
  }
  $output = $events;
}
if ($output !== "") {
  header("Content-Type: application/json");
  print json_encode($output);
}
