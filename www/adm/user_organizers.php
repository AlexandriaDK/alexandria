<?php
// save page for users editing organizers

require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$convent = (int) $_REQUEST['convent'];
$action = (string) $_REQUEST['action'];
$user_id = $_SESSION['user_id'];
$token = $_REQUEST['token'] ?? '';
$acrel_id = (int) $_REQUEST['acrel_id'];

if (!$user_id) {
	header("Location: ../data?con=$convent");
	exit;
}

if ( $action ) {
	validatetoken( $token );
}


// valid user

// Get id or text
$role = (string) $_REQUEST['role'];
$aut_text = (string) $_REQUEST['aut_text'];
$aut_id = (int) $aut_text;
$aut_extra = "";
if (!$aut_id) {
	$aut_extra = $aut_text;
	$aut_id = NULL;
}

if (getone("SELECT 1 FROM convent WHERE id = $convent") != 1) { // check if congress exists
	die("DB error");
	header("Location: ../");
	exit;
}

if ($action == 'add' && ($aut_id || $aut_extra) ) {
	$r = doquery("
		INSERT INTO acrel (aut_id, convent_id, aut_extra, role, added_by_user_id)
		VALUES (" . strNullEscape($aut_id) . ", $convent, '" . dbesc($aut_extra) . "', '" . dbesc($role) . "', $user_id)
	");
	if ($acrel_id = dbid($dblink) ) {
		$_SESSION['can_edit_organizers'][$acrel_id] = TRUE;
		award_achievement(91);
		chlog($convent,'convent','Organizer added: ' . ( $aut_id ? $aut_id : $aut_extra ));
	}
} elseif ($action == 'delete') {
	if ( $_SESSION['user_editor'] || $_SESSION['user_admin'] || $_SESSION['can_edit_organizers'][$acrel_id] ) {
		doquery("DELETE FROM acrel WHERE id = $acrel_id");
		chlog($convent,'convent','Organizer removed');
	}
}

header("Location: ../data?con=$convent&edit=organizers#organizers");
exit;
?>
