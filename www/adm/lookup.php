<?php
// Lookup service for various editor pages.

require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$type = (string) $_REQUEST['type'];
$label = trim( (string) $_REQUEST['label'] );
$id = (int) $_REQUEST['currentid'];
$term = (string) $_REQUEST['term'] ?? '';

function resultexit( $data ) {
	print json_encode( $data );
	exit;
}

if ($type == 'sce' && $label != "") {
	$num = getone("SELECT COUNT(*) FROM sce WHERE title = '" . dbesc($label) . "'");
	print $num;
}

if ($type == 'games' && $term !== "") {
	$games = getcol("SELECT CONCAT(id, ' - ', title) AS label FROM sce WHERE title LIKE '" . dbesc($term) . "%'");
	header("Content-Type: application/json");
	print json_encode( $games );
	exit;
}

if ($type == 'countrycode' && $label != "") {
	$countryname = getCountryName($label);
	print $countryname;	
}

if ($type == 'languagecode' && $label != "") {
	$languagename = getLanguageName($label);
	print $languagename;
}

if ($type == 'articlereference' && $term !== "") {
	$escapequery = dbesc($term);
	$refs = getcol("
		SELECT CONCAT('tag', tag.id, ' - ', tag) AS label FROM tag WHERE tag LIKE '$escapequery%'
		UNION ALL
		SELECT CONCAT('cs', conset.id, ' - ', name) AS label FROM conset WHERE name LIKE '$escapequery%'
		UNION ALL
		SELECT CONCAT('c', convent.id, ' - ', convent.name, ' (', COALESCE(year,'?'), ')') AS label FROM convent
		INNER JOIN conset ON convent.conset_id = conset.id
		WHERE convent.name LIKE '$escapequery%'
		OR CONCAT(convent.name,' (',year,')') LIKE '$escapequery%'
		OR CONCAT(convent.name,' ',year) LIKE '$escapequery%'
		OR CONCAT(conset.name, ' ', convent.year) LIKE '$escapequery%'
		OR (
			'$escapequery' REGEXP ' [0-9][0-9]$' AND
			CONCAT(conset.name, ' ', convent.year) = CONCAT(LEFT('$escapequery', (LENGTH('$escapequery') -3)), ' 19', RIGHT('$escapequery', 2))
		)
		OR CONCAT(conset.name,' (',year,')') LIKE '$escapequery%'
		UNION ALL
		SELECT CONCAT('sys', sys.id, ' - ', name) AS label FROM sys WHERE name LIKE '$escapequery%'
		UNION ALL
		SELECT CONCAT('m', magazine.id, ' - ', name) AS label FROM magazine WHERE name LIKE '$escapequery%'
		UNION ALL
		SELECT CONCAT('g', sce.id, ' - ', title) AS label FROM sce WHERE title LIKE '$escapequery%'
		UNION ALL
		SELECT CONCAT('p', aut.id, ' - ', firstname,' ',surname) AS label FROM aut WHERE CONCAT(firstname,' ',surname) LIKE '$escapequery%'
		UNION
		SELECT CONCAT('p', aut.id, ' - ', firstname,' ',surname) AS label FROM aut WHERE CONCAT(surname,' ',firstname) LIKE '$escapequery%'
	");
	header("Content-Type: application/json");
	print json_encode( $refs );
	exit;
}


if ( $type == 'addperson' && $label != "" ) {
	if ( $pid = intval( $label ) ) {
		resultexit( [ "new" => false, "error" => false, "id" => $pid, "msg" => "Existing user" ] );
	}
	$result = [];
	$name = $label;
	if (strpos($name, " ") === FALSE) {
		resultexit( [ "new" => false, "error" => true, "msg" => "No space in name" ] );
	}
	$pos = strrpos($name, " ");
	$surname = substr($name, $pos+1);
	$firstname = substr($name, 0, $pos);
	$rid = getone("SELECT id FROM aut WHERE firstname = '" . dbesc( $firstname ) . "' AND surname = '" . dbesc( $surname ) . "'");
	if ( $rid ) {
		resultexit( [ "new" => false, "error" => false, "id" => $rid, "msg" => "Existing user" ] );
	}
	$q = "INSERT INTO aut (firstname, surname) VALUES ('" . dbesc( $firstname ) . "', '" . dbesc( $surname ) . "')";
	if ($r = doquery( $q ) ) {
		$pid = dbid();
		chlog($pid,'aut',"Person created");
		resultexit( [ "new" => true, "error" => false, "id" => $pid, "msg" => "Person created" ] );
	} else {
		resultexit( [ "new" => false, "error" => true, "msg" => "Database error" ] );
	}
}

?>
