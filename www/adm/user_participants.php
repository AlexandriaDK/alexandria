<?php
// save page for users editing amount of participants for a game
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";

$token = $_REQUEST['token'] ?? '';
$scenarie = (int) $_REQUEST['scenarie'];
$user_id = $_SESSION['user_id'];
if (!$user_id || !$scenarie) {
  header("Location: ../data?scenarie=$scenarie");
  exit;
}

validatetoken($token);

// valid user
$gms = $_REQUEST['gms'];
$players = $_REQUEST['players'];
list($gms_min, $gms_max) = strSplitParticipants($gms);
list($players_min, $players_max) = strSplitParticipants($players);

$q = getone("SELECT 1 FROM game WHERE id = $scenarie");
if ($q != 1) { // check if scenario exists
  header("Location: ../data?scenarie=$scenarie");
  exit;
}
$q = getone("SELECT 1 FROM game WHERE id = $scenarie AND gms_min IS null AND gms_max IS null AND players_min IS null AND players_max IS null");

if (!($_SESSION['user_editor'] || $_SESSION['user_admin'] || $_SESSION['can_edit_participant'][$scenarie] || $r)) {
  header("Location: ../data?scenarie=$scenarie");
  exit;
}

doquery("UPDATE game SET gms_min = " . strNullEscape($gms_min) . ", gms_max = " . strNullEscape($gms_max) . ", players_min = " . strNullEscape($players_min) . ", players_max = " . strNullEscape($players_max) . " WHERE id = $scenarie");
chlog($scenarie, 'game', 'Participants updated');
award_achievement(82);

$_SESSION['can_edit_participant'][$scenarie] = true;

header("Location: ../data?scenarie=$scenarie");
exit;
