<?php
/*
Ideer til forside:
nyeste tilføjelser
nyeste anmeldelser
ugens scenarie?
links til div. top 10-lister - eller direkte på forsiden?
links til genrer? systemer?
afstemning?
link til "min side" - den personlige login
link til con, scenarie, forfatter oversigt?
søgefelt
kommende cons

Forslag: Citatboks, folks citater
*/

require("./connect.php");
require("base.inc.php");

$person = (int) ($_REQUEST['person'] ?? 0);
$scenarie = (int) ($_REQUEST['scenarie'] ?? 0);
$game = (int) ($_REQUEST['game'] ?? 0);
$con = (int) ($_REQUEST['con'] ?? 0);
$conset = (int) ($_REQUEST['conset'] ?? 0);
$system = (int) ($_REQUEST['system'] ?? 0);
$year = (int) ($_REQUEST['year'] ?? 0);
$tag = (string) ($_REQUEST['tag'] ?? '');

if ($person) {
	include ("person.inc.php");
} elseif ($scenarie || $game) {
	include ("game.inc.php");
} elseif ($con) {
	include ("convent.inc.php");
} elseif ($conset) {
	include ("conset.inc.php");
} elseif ($system) {
	include ("system.inc.php");
} elseif ($year) {
	include ("year.inc.php");
} elseif ($tag) {
	include ("tag.inc.php");
} else {
	include ("default.inc.php");
}
?>
