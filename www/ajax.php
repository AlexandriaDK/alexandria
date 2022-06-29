<?php
require("./connect.php");
require("base.inc.php");

$term = (string) ($_REQUEST['query'] ?? $_REQUEST['term'] ?? '');
$type = (string) ($_REQUEST['type'] ?? 'website');

$escapequery = dbesc($term);
$likeescapequery = likeesc($term);
$result = $suggestions = [];
$separator = "__SEPARATOR__";
$separator_limit = 3;

if (strlen($term) >= 2) {
	$query = "
			SELECT person.id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(game.title ORDER BY game.popularity DESC SEPARATOR '$separator'), '') AS note FROM person LEFT JOIN pgrel ON person.id = pgrel.person_id AND pgrel.title_id IN (1,5) LEFT JOIN game ON pgrel.game_id = game.id WHERE CONCAT(firstname,' ',surname) LIKE '$likeescapequery%' GROUP BY person.id
		UNION
			SELECT person.id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(game.title ORDER BY game.popularity DESC SEPARATOR '$separator'), '') AS note FROM person LEFT JOIN pgrel ON person.id = pgrel.person_id AND pgrel.title_id IN (1,5) LEFT JOIN game ON pgrel.game_id = game.id WHERE CONCAT(surname, ' ', firstname) LIKE '$likeescapequery%' GROUP BY person.id
		UNION ALL
			SELECT game.id, title AS label, 'sce' AS type, 'scenarie' AS linkpart, 'scenarie' AS filepart, COALESCE(GROUP_CONCAT(CONCAT(person.firstname,' ',person.surname) ORDER BY person.popularity DESC, person.id SEPARATOR '$separator'), '') AS note FROM game LEFT JOIN pgrel ON game.id = pgrel.game_id AND pgrel.title_id IN (1,5) LEFT JOIN person ON pgrel.person_id = person.id  WHERE title LIKE '$likeescapequery%' GROUP BY game.id
		UNION ALL
			SELECT gamesystem.id, name AS label, 'gamesystem' AS type, 'system' AS linkpart, 'system' AS filepart, COALESCE(GROUP_CONCAT(game.title ORDER BY game.popularity DESC SEPARATOR '$separator'), '') AS note FROM gamesystem LEFT JOIN game ON gamesystem.id = game.sys_id WHERE name LIKE '$likeescapequery%' GROUP BY gamesystem.id
		UNION ALL
			SELECT convention.id, CONCAT(convention.name,' (',convention.year,')') AS label, 'convention' AS type, 'con' AS linkpart, 'convent' AS filepath, '' AS note FROM convention
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
		UNION ALL
			SELECT magazine.id, name COLLATE utf8mb4_danish_ci, 'magazine' AS type, 'magazine' AS linkpart, 'magazine' AS filepart, '📚 (magazine)' AS note FROM magazine WHERE name LIKE '$likeescapequery%' 
		UNION ALL
			SELECT tag, tag AS label, 'tag' AS type, 'tag' AS linkpart, 'tag' AS filepart, '🏷️ (tag)' AS note FROM tags WHERE tag LIKE '$likeescapequery%' GROUP BY tag
		UNION
			SELECT tag, tag AS label, 'tag' AS type, 'tag' AS linkpart, 'tag' AS filepart, '🏷️ (tag)' AS note FROM tag WHERE tag LIKE '$likeescapequery%' GROUP BY tag
		ORDER BY label
	";
	
	$all = getall($query, FALSE);
	print dberror();

	foreach($all AS &$data) {
		$suggestions[] = $data['label'];
		$picfile = "gfx/" . $data['filepart'] . "/s_" . $data['id'] . ".jpg";
		if (file_exists($picfile) ) {
			$data['thumbnail'] = $picfile;
		}
		if ( in_array( $data['type'], ['aut', 'sce', 'gamesystem'] ) ) { // max 3 ($separator_limit) items
			$anote = explode( $separator, $data['note'] );
			$note = implode( ", ", array_slice( $anote, 0, $separator_limit ) );
			if ( count( $anote ) > $separator_limit ) {
				$note .= ", …";
			}
			$data['note'] = $note;

		}
	}

	$result = $all;
}

if ($type == 'suggestions') {
	header("Content-Type: application/x-suggestions+json");
	$suggestionsresult = [ $term, $suggestions ];
	print json_encode($suggestionsresult);
} else {
	header("Content-Type: application/json");
	print json_encode($result);
}

?>
