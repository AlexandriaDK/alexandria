<?php
// save page for users editing organizers

require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$convention = (int) $_REQUEST['convention'];
$action = (string) $_REQUEST['action'];
$user_id = $_SESSION['user_id'];
$token = $_REQUEST['token'] ?? '';
$pcrel_id = (int) ($_REQUEST['pcrel_id'] ?? 0);

if (!$user_id) {
	header("Location: ../data?con=$convent");
	exit;
}

if ($action) {
	validatetoken($token);
}


// valid user

// Get id or text
$role = trim((string) $_REQUEST['role']);
$person_text = trim((string) $_REQUEST['person_text']);
$person_id = (int) $person_text;
$person_extra = "";
if (!$person_id) {
	$person_extra = $person_text;
	$person_id = NULL;
}

if (getone("SELECT 1 FROM convention WHERE id = $convention") != 1) { // check if congress exists
	die("DB error");
	header("Location: ../");
	exit;
}

if ($action == 'add' && ($person_id || $person_extra)) {
	$r = doquery("
		INSERT INTO pcrel (person_id, convention_id, person_extra, role, added_by_user_id)
		VALUES (" . strNullEscape($person_id) . ", $convention, '" . dbesc($person_extra) . "', '" . dbesc($role) . "', $user_id)
	");
	if ($pcrel_id = dbid()) {
		$_SESSION['can_edit_organizers'][$pcrel_id] = TRUE;
		award_achievement(91);
		chlog($convention, 'convent', 'Organizer added: ' . ($person_id ? $person_id : $person_extra));
	}
} elseif ($action == 'delete') {
	if ($_SESSION['user_editor'] || $_SESSION['user_admin'] || $_SESSION['can_edit_organizers'][$pcrel_id]) {
		doquery("DELETE FROM pcrel WHERE id = $pcrel_id");
		chlog($convention, 'convent', 'Organizer removed');
	}
}

header("Location: ../data?con=$convention&edit=organizers#organizers");
exit;
