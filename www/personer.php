<?php
require("./connect.php");
require("base.inc.php");

$b = (string) ($_REQUEST['b'] ?? ""); // search letter
$s = (string) ($_REQUEST['s'] ?? ""); // sort; "f" = first name, "e" = surname

if (mb_strlen($b) != 1) {
	$b = "a";
}

// default: surname
if ($s != "f" && $s != "e") {
	$s = "e";
}

if ($s == "f") award_achievement(34);

// find ud af hvad, der skal sorteres efter - efternavn er default
switch ($s) {
	case "f":
	$orderby = "firstname, surname, id";
	$orderfield = "firstname";
	break;
	
	case "e":
	default:
	$orderby = "surname, firstname, id";
	$orderfield = "surname";
}

// fetch letters
// we are currently just fetching letters one by one
$chars = range('a','z');
$chars[] = "æ";
$chars[] = "ø";
$chars[] = "å";
$chars[] = "#";

$charout = "";
foreach($chars AS $char) {
	if ($s == "f") {
		$charout .= "\n\t\t\t<a href=\"personer?b=".rawurlencode($char)."&amp;s=$s\">".mb_strtoupper($char)."</a>";
	} else {
		$charout .= "\n\t\t\t<a href=\"personer?b=".rawurlencode($char)."\">".mb_strtoupper($char)."</a>";
	}
}

if ( $b != '#' && ! in_array($b, $chars) ) {
	$b = 'a';
}

if ( $b != '#') {
	$persons = getall("SELECT id, firstname, surname FROM aut WHERE $orderfield LIKE '$b%' ORDER BY $orderby");
} else {
	$persons = getall("SELECT id, firstname, surname FROM aut WHERE $orderfield NOT REGEXP '^[A-ZÆØÅ]' ORDER BY $orderby");
}
$no = 0;
$list = "";

foreach ($persons AS $r) {
	if ($orderfield == "surname") {
		$list .= "<a href=\"data?person={$r['id']}\">{$r['surname']}, {$r['firstname']}</a><br />\n";
	} elseif ($orderfield == "firstname") {
		$list .= "<a href=\"data?person={$r['id']}\">{$r['firstname']} {$r['surname']}</a><br />\n";
	}

}

// Smarty
$t->assign('b',$b);
$t->assign('s',$s);
$t->assign('chars',$charout);
$t->assign('list',$list);

$t->display('persons.tpl');

?>
