<?php
// save page for users editing amount of participants for a game

function strSplitParticipants($str) {
	if (!preg_match('/^\d+(-\d+)?$/',$str) ) {
		return [ NULL, NULL ];
	}
	list ($str_min, $str_max) = explode("-", $str);
	if (!$str_max) {
		$str_max = $str_min;
	} elseif ($str_min > $str_max) {
		$str_tmp = $str_min;
		$str_min = $str_max;
		$str_max = $str_tmp;
	}
	return [ $str_min, $str_max ];
}

require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$scenarie = (int) $_REQUEST['scenarie'];
$user_id = $_SESSION['user_id'];
if (!$user_id || !$scenarie) {
	header("Location: ../data?scenarie=$scenarie");
	exit;
}

// valid user
$gms = $_REQUEST['gms'];
$players = $_REQUEST['players'];
list ($gms_min, $gms_max) = strSplitParticipants($gms);
list ($players_min, $players_max) = strSplitParticipants($players);

$q = getone("SELECT 1 FROM sce WHERE id = $scenarie");
if ($q != 1) { // check if scenario exists
	header("Location: ../data?scenarie=$scenarie");
	exit;
}
$q = getone("SELECT 1 FROM sce WHERE id = $scenarie AND gms_min IS NULL AND gms_max IS NULL AND players_min IS NULL AND players_max IS NULL");

if (!($_SESSION['user_editor'] || $_SESSION['user_admin'] || $_SESSION['can_edit_participant'][$scenarie] || $r) ) {
	header("Location: ../data?scenarie=$scenarie");
	exit;
}

doquery("UPDATE sce SET gms_min = " . strNullEscape($gms_min) . ", gms_max = " . strNullEscape($gms_max) . ", players_min = " . strNullEscape($players_min) . ", players_max = " . strNullEscape($players_max) . " WHERE id = $scenarie");
chlog($scenarie,'sce','Deltagerantal rettet');
award_achievement(82);

$_SESSION['can_edit_participant'][$scenarie] = TRUE;

header("Location: ../data?scenarie=$scenarie");
exit;
?>
