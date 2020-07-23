<?php
// Lookup service for various editor pages.

require "adm.inc";
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
		chlog($pid,'aut',"Person oprettet");
		resultexit( [ "new" => true, "error" => false, "id" => $pid, "msg" => "Person created" ] );
	} else {
		resultexit( [ "new" => false, "error" => true, "msg" => "Database error" ] );
	}
}

?>
