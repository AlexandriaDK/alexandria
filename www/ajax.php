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
			SELECT aut.id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(sce.title ORDER BY sce.popularity DESC SEPARATOR '$separator'), '') AS note FROM aut LEFT JOIN asrel ON aut.id = asrel.aut_id AND asrel.tit_id IN (1,5) LEFT JOIN sce ON asrel.sce_id = sce.id WHERE CONCAT(firstname,' ',surname) LIKE '$likeescapequery%' GROUP BY aut.id
		UNION
			SELECT aut.id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(sce.title ORDER BY sce.popularity DESC SEPARATOR '$separator'), '') AS note FROM aut LEFT JOIN asrel ON aut.id = asrel.aut_id AND asrel.tit_id IN (1,5) LEFT JOIN sce ON asrel.sce_id = sce.id WHERE CONCAT(surname, ' ', firstname) LIKE '$likeescapequery%' GROUP BY aut.id
		UNION ALL
			SELECT sce.id, title AS label, 'sce' AS type, 'scenarie' AS linkpart, 'scenarie' AS filepart, COALESCE(GROUP_CONCAT(CONCAT(aut.firstname,' ',aut.surname) ORDER BY aut.popularity DESC, aut.id SEPARATOR '$separator'), '') AS note FROM sce LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id IN (1,5) LEFT JOIN aut ON asrel.aut_id = aut.id  WHERE title LIKE '$likeescapequery%' GROUP BY sce.id
		UNION ALL
			SELECT sys.id, name AS label, 'sys' AS type, 'system' AS linkpart, 'system' AS filepart, COALESCE(GROUP_CONCAT(sce.title ORDER BY sce.popularity DESC SEPARATOR '$separator'), '') AS note FROM sys LEFT JOIN sce ON sys.id = sce.sys_id WHERE name LIKE '$likeescapequery%' GROUP BY sys.id
		UNION ALL
			SELECT convent.id, CONCAT(convent.name,' (',convent.year,')') AS label, 'convent' AS type, 'con' AS linkpart, 'convent' AS filepath, '' AS note FROM convent
			INNER JOIN conset ON convent.conset_id = conset.id
			WHERE convent.name LIKE '$likeescapequery%'
			OR CONCAT(convent.name,' (',year,')') LIKE '$likeescapequery%'
			OR CONCAT(convent.name,' ',year) LIKE '$likeescapequery%'
			OR CONCAT(conset.name, ' ', convent.year) LIKE '$likeescapequery%'
			OR (
				'$escapequery' REGEXP ' [0-9][0-9]$' AND
				CONCAT(conset.name, ' ', RIGHT(convent.year,2) ) = CONCAT(LEFT('$escapequery', (LENGTH('$escapequery') -3)), ' ', RIGHT('$escapequery', 2))
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
		if ( in_array( $data['type'], ['aut', 'sce', 'sys'] ) ) { // max 3 ($separator_limit) items
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
