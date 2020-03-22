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
require("base.inc");

$person = isset($_REQUEST['person']) ? intval($_REQUEST['person']) : 0;
$scenarie = isset($_REQUEST['scenarie']) ? intval($_REQUEST['scenarie']) : 0;
$game = isset($_REQUEST['game']) ? intval($_REQUEST['game']) : 0;
$con = isset($_REQUEST['con']) ? intval($_REQUEST['con']) : 0;
$conset = isset($_REQUEST['conset']) ? intval($_REQUEST['conset']) : 0;
$system = isset($_REQUEST['system']) ? intval($_REQUEST['system']) : 0;
$year = isset($_REQUEST['year']) ? intval($_REQUEST['year']) : 0;
$tag = isset($_REQUEST['tag']) ? (string) $_REQUEST['tag'] : NULL;

if ($person) {
	include ("person_t.inc");
} elseif ($scenarie) {
	include ("game_t.inc");
//	include ("scenario_t.inc");
} elseif ($game) {
	include ("game_t.inc");
} elseif ($con) {
	include ("convent_t.inc");
} elseif ($conset) {
	include ("conset_t.inc");
} elseif ($system) {
	include ("system_t.inc");
} elseif ($year) {
	include ("year_t.inc");
} elseif ($tag) {
	include ("tag_t.inc");
} else {
	include ("default.inc");
}
?>
