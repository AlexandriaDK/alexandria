<?php
require("./connect.php");
require("base.inc");

header("Content-Type: application/json");

$query = (string) $_REQUEST['query'];
if (!$query) {
	$query = (string) $_REQUEST['term'];
}
$escapequery = dbesc($query);
$result = [];

if (strlen($query) >= 2) {
	$query = "
			SELECT aut.id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(sce.title ORDER BY sce.popularity DESC SEPARATOR ', '), '') AS note FROM aut LEFT JOIN asrel ON aut.id = asrel.aut_id AND asrel.tit_id IN (1,5) LEFT JOIN sce ON asrel.sce_id = sce.id WHERE CONCAT(firstname,' ',surname) LIKE '$escapequery%' GROUP BY aut.id
		UNION ALL
			SELECT aut.id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart, 'person' AS filepart, COALESCE(GROUP_CONCAT(sce.title ORDER BY sce.popularity DESC SEPARATOR ', '), '') AS note FROM aut LEFT JOIN asrel ON aut.id = asrel.aut_id AND asrel.tit_id IN (1,5) LEFT JOIN sce ON asrel.sce_id = sce.id WHERE CONCAT(surname, ' ', firstname) LIKE '$escapequery%' GROUP BY aut.id
		UNION ALL
			SELECT sce.id, title AS label, 'sce' AS type, 'scenarie' AS linkpart, 'scenarie' AS filepart, COALESCE(GROUP_CONCAT(CONCAT(aut.firstname,' ',aut.surname) SEPARATOR ', '), '') AS note FROM sce LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id IN (1,5) LEFT JOIN aut ON asrel.aut_id = aut.id  WHERE title LIKE '$escapequery%' GROUP BY sce.id
		UNION ALL
			SELECT sys.id, name AS label, 'sys' AS type, 'system' AS linkpart, 'system' AS filepart, MIN(sce.title) AS note FROM sys LEFT JOIN sce ON sys.id = sce.sys_id WHERE name LIKE '$escapequery%' GROUP BY sys.id
		UNION ALL
			SELECT convent.id, CONCAT(name,' (',year,')') AS label, 'convent' AS type, 'con' AS linkpart, 'convent' AS filepath, '' AS note FROM convent WHERE name LIKE '$escapequery%' OR CONCAT(name,' (',year,')') LIKE '$escapequery%' OR CONCAT(name,' ',year) LIKE '$escapequery%'
		ORDER BY label
	";

	$all = getall($query, FALSE);

	foreach($all AS &$data) {
		$picfile = "gfx/" . $data['filepart'] . "/s_" . $data['id'] . ".jpg";
		if (file_exists($picfile) ) {
			$data['thumbnail'] = $picfile;
		}
	}

	$result = $all;
}

print json_encode($result);

?>
