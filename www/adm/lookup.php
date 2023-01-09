<?php
// Lookup service for various editor pages.

require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$type = (string) ($_REQUEST['type'] ?? '');
$label = trim((string) ($_REQUEST['label'] ?? ''));
$id = (int) ($_REQUEST['currentid'] ?? 0);
$term = (string) ($_REQUEST['term'] ?? '');

function resultexit($data)
{
	print json_encode($data);
	exit;
}

if ($type == 'game' && $label != "") {
	$num = getone("SELECT COUNT(*) FROM game WHERE title = '" . dbesc($label) . "'");
	print $num;
}

if ($type == 'games' && $term !== "") {
	$games = getcol("SELECT CONCAT(id, ' - ', title) AS label FROM game WHERE title LIKE '" . dbesc($term) . "%'");
	header("Content-Type: application/json");
	print json_encode($games);
	exit;
}

if ($type == 'countrycode' && $label != "") {
	$countryname = getCountryName($label);
	print $countryname;
}

if ($type == 'consetcountrycode' && $label != "") {
	$conset_id = (int) $label;
	$countrycode = getone("SELECT country FROM conset WHERE id = $conset_id");
	if ($countrycode) {
		$countryname = getCountryName($countrycode);
		print $countryname;
	}
}

if ($type == 'languagecode' && $label != "") {
	$languagename = getLanguageName($label);
	print $languagename;
}

if ($type == 'person' && $term !== "") {
	$escapequery = dbesc($term);
	$likeescapequery = likeesc($term);
	$refs = getcol("
		SELECT CONCAT(person.id, ' - ', firstname,' ',surname) AS label FROM person WHERE CONCAT(firstname,' ',surname) LIKE '$likeescapequery%'
		UNION
		SELECT CONCAT(person.id, ' - ', firstname,' ',surname) AS label FROM person WHERE CONCAT(surname,' ',firstname) LIKE '$likeescapequery%'
	");
	header("Content-Type: application/json");
	print json_encode($refs);
	exit;
}

if ($type == 'game' && $term !== "") {
	$escapequery = dbesc($term);
	$likeescapequery = likeesc($term);
	$refs = getcol("
		SELECT CONCAT(g.id, ' - ', title) AS label FROM game g WHERE title LIKE '$likeescapequery%'
	");
	header("Content-Type: application/json");
	print json_encode($refs);
	exit;
}

if ($type == 'articlereference' && $term !== "") {
	$escapequery = dbesc($term);
	$likeescapequery = likeesc($term);
	$refs = getcol("
		SELECT CONCAT('tag', tag.id, ' - ', tag) AS label FROM tag WHERE tag LIKE '$likeescapequery%'
		UNION ALL
		SELECT CONCAT('cs', conset.id, ' - ', name) AS label FROM conset WHERE name LIKE '$likeescapequery%'
		UNION ALL
		SELECT CONCAT('c', c.id, ' - ', c.name, ' (', COALESCE(year,'?'), ')') AS label FROM convention c
		INNER JOIN conset ON c.conset_id = conset.id
		WHERE c.name LIKE '$likeescapequery%'
		OR CONCAT(c.name,' (',year,')') LIKE '$likeescapequery%'
		OR CONCAT(c.name,' ',year) LIKE '$likeescapequery%'
		OR CONCAT(conset.name, ' ', c.year) LIKE '$likeescapequery%'
		OR (
			'$escapequery' REGEXP ' [0-9][0-9]$' AND
			CONCAT(conset.name, ' ', RIGHT(c.year,2) ) = CONCAT(LEFT('$escapequery', (LENGTH('$escapequery') -3)), ' ', RIGHT('$escapequery', 2))
			)
		OR CONCAT(conset.name,' (',year,')') LIKE '$likeescapequery%'
		UNION ALL
		SELECT CONCAT('sys', gs.id, ' - ', name) AS label FROM gamesystem gs WHERE name LIKE '$likeescapequery%'
		UNION ALL
		SELECT CONCAT('m', magazine.id, ' - ', name) AS label FROM magazine WHERE name LIKE '$likeescapequery%'
		UNION ALL
		SELECT CONCAT('g', g.id, ' - ', title) AS label FROM game g WHERE title LIKE '$likeescapequery%'
		UNION ALL
		SELECT CONCAT('p', person.id, ' - ', firstname,' ',surname) AS label FROM person WHERE CONCAT(firstname,' ',surname) LIKE '$likeescapequery%'
		UNION
		SELECT CONCAT('p', person.id, ' - ', firstname,' ',surname) AS label FROM person WHERE CONCAT(surname,' ',firstname) LIKE '$likeescapequery%'
	");
	header("Content-Type: application/json");
	print json_encode($refs);
	exit;
}


if ($type == 'addperson' && $label != "") {
	if ($pid = intval($label)) {
		resultexit(["new" => false, "error" => false, "id" => $pid, "msg" => "Existing user"]);
	}
	$result = [];
	$name = $label;
	if (strpos($name, " ") === FALSE) {
		resultexit(["new" => false, "error" => true, "msg" => "No space in name"]);
	}
	$pos = strrpos($name, " ");
	$surname = substr($name, $pos + 1);
	$firstname = substr($name, 0, $pos);
	$rid = getone("SELECT id FROM person WHERE firstname = '" . dbesc($firstname) . "' AND surname = '" . dbesc($surname) . "'");
	if ($rid) {
		resultexit(["new" => false, "error" => false, "id" => $rid, "msg" => "Existing user"]);
	}
	$q = "INSERT INTO person (firstname, surname) VALUES ('" . dbesc($firstname) . "', '" . dbesc($surname) . "')";
	if ($r = doquery($q)) {
		$pid = dbid();
		chlog($pid, 'person', "Person created");
		resultexit(["new" => true, "error" => false, "id" => $pid, "msg" => "Person created"]);
	} else {
		resultexit(["new" => false, "error" => true, "msg" => "Database error"]);
	}
}
