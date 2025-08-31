<?php
require("./connect.php");
require("base.inc.php");

$term = (string) ($_REQUEST['query'] ?? $_REQUEST['term'] ?? '');
$result = (string) ($_REQUEST['result'] ?? 'website');
$type = (string) ($_REQUEST['type'] ?? '');
$with_id = (bool) ($_REQUEST['with_id'] ?? false);

$escapequery = dbesc($term);
$likeescapequery = likeesc($term);
$result = $suggestions = [];
$separator = "__SEPARATOR__";
$separator_limit = 3;

if (strlen($term) >= 2) {
  $queryparts = [];
  if ($type == 'person' || $type == '') {
    $queryparts[] = "
		SELECT person.id, CONCAT(firstname,' ',surname) AS label, 'person' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(game.title ORDER BY game.popularity DESC SEPARATOR '$separator'), '') AS note FROM person LEFT JOIN pgrel ON person.id = pgrel.person_id AND pgrel.title_id IN (1,5) LEFT JOIN game ON pgrel.game_id = game.id WHERE CONCAT(firstname,' ',surname) LIKE '$likeescapequery%' GROUP BY person.id
		UNION
		SELECT person.id, CONCAT(firstname,' ',surname) AS label, 'person' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(game.title ORDER BY game.popularity DESC SEPARATOR '$separator'), '') AS note FROM person LEFT JOIN pgrel ON person.id = pgrel.person_id AND pgrel.title_id IN (1,5) LEFT JOIN game ON pgrel.game_id = game.id WHERE CONCAT(surname, ' ', firstname) LIKE '$likeescapequery%' GROUP BY person.id
		";
  }
  if ($type == 'game' || $type == '') {
    $queryparts[] = "
			SELECT game.id, title AS label, 'game' AS type, 'scenarie' AS linkpart, 'scenarie' AS filepart, COALESCE(GROUP_CONCAT(CONCAT(person.firstname,' ',person.surname) ORDER BY person.popularity DESC, person.id SEPARATOR '$separator'), '') AS note
			FROM game
			LEFT JOIN pgrel ON game.id = pgrel.game_id AND pgrel.title_id IN (1,5)
			LEFT JOIN person ON pgrel.person_id = person.id
			WHERE title LIKE '$likeescapequery%'
			GROUP BY game.id
		";
  }
  if ($type == 'gamesystem' || $type == '') {
    $queryparts[] = "SELECT gamesystem.id, name AS label, 'gamesystem' AS type, 'system' AS linkpart, 'system' AS filepart, COALESCE(GROUP_CONCAT(game.title ORDER BY game.popularity DESC SEPARATOR '$separator'), '') AS note FROM gamesystem LEFT JOIN game ON gamesystem.id = game.gamesystem_id WHERE name LIKE '$likeescapequery%' GROUP BY gamesystem.id";
  }
  if ($type == 'convention' || $type == '') {
    $queryparts[] = "
		SELECT convention.id, CONCAT(convention.name,' (',COALESCE(convention.year,'?'),')') AS label, 'convention' AS type, 'con' AS linkpart, 'convent' AS filepart, '' AS note FROM convention
		INNER JOIN conset ON convention.conset_id = conset.id
		WHERE convention.name LIKE '$likeescapequery%'
		OR CONCAT(convention.name,' (',year,')') LIKE '$likeescapequery%'
		OR CONCAT(convention.name,' ',year) LIKE '$likeescapequery%'
		OR CONCAT(conset.name, ' ', convention.year) LIKE '$likeescapequery%'
		OR (
			'$escapequery' REGEXP ' [0-9][0-9]$' AND
			CONCAT(conset.name, ' ', RIGHT(convention.year,2) ) = CONCAT(LEFT('$escapequery', (LENGTH('$escapequery') -3)), ' ', RIGHT('$escapequery', 2))
		)
		OR CONCAT(conset.name,' (',year,')') LIKE '$likeescapequery%'
		";
  }
  if ($type == 'magazine' || $type == '') {
    $queryparts[] = "SELECT magazine.id, name COLLATE utf8mb4_danish_ci, 'magazine' AS type, 'magazine' AS linkpart, 'magazine' AS filepart, '📚 (magazine)' AS note FROM magazine WHERE name LIKE '$likeescapequery%'";
  }
  if ($type == 'tag' || $type == '') {
    $queryparts[] = "
		SELECT tag, tag AS label, 'tag' AS type, 'tag' AS linkpart, 'tag' AS filepart, '🏷️ (tag)' AS note FROM tags WHERE tag LIKE '$likeescapequery%' GROUP BY tag
		UNION
		SELECT tag, tag AS label, 'tag' AS type, 'tag' AS linkpart, 'tag' AS filepart, '🏷️ (tag)' AS note FROM tag WHERE tag LIKE '$likeescapequery%' GROUP BY tag
		";
  }
  if ($type == 'location' || $type == '') {
    $queryparts[] = "SELECT locations.id, name AS label, 'location' AS type, 'locations' AS linkpart, '' AS filepart, '📍 (location)' AS note FROM locations WHERE name LIKE '$likeescapequery%' GROUP BY locations.id";
  }
  if (!$queryparts) { // Unknown type
    print json_encode([]);
    exit;
  }
  $query = implode(' UNION ALL ', $queryparts);
  $all = getall($query, false);
  print dberror();

  foreach ($all as &$data) {
    $suggestions[] = $data['label'];
    $picfile = "gfx/" . $data['filepart'] . "/s_" . ($data['id'] ?? 0) . ".jpg";
    if (file_exists($picfile)) {
      $data['thumbnail'] = $picfile;
    }
    // add locations to preview list
    if (in_array($data['type'], ['person', 'game', 'gamesystem'])) { // max 3 ($separator_limit) items
      $anote = explode($separator, $data['note']);
      $note = implode(", ", array_slice($anote, 0, $separator_limit));
      if (count($anote) > $separator_limit) {
        $note .= ", …";
      }
      $data['note'] = $note;
    }
    if ($with_id) {
      $data['label'] = $data['id'] . ' - ' . $data['label'];
    }
  }
  $result = $all;
}

if ($result == 'suggestions') {
  header("Content-Type: application/x-suggestions+json");
  $suggestionsresult = [$term, $suggestions];
  print json_encode($suggestionsresult);
} else {
  header("Content-Type: application/json");
  print json_encode($result);
}
