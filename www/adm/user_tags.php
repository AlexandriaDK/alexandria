<?php
// save page for users editing tags for scenario

require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$scenario = (int) $_REQUEST['scenario'];
$action = (string) $_REQUEST['action'];
$user_id = $_SESSION['user_id'];
if (!$user_id || !$scenario) {
	header("Location: ../data?scenarie=$scenario");
	exit;
}

// valid user
$tag = trim( (string) $_REQUEST['tag'] );
$tag_id = (int) $_REQUEST['tag_id'];

$q = getone("SELECT 1 FROM sce WHERE id = $scenario");
if ($q != 1) { // check if scenario exists - should probably redirect somewhere else
	header("Location: ../data?scenarie=$scenario");
	exit;
}

if ($action == 'add') {
	$q = getone("SELECT 1 FROM tags WHERE sce_id = $scenario AND tag = '" . dbesc($tag) . "'");
	if ($q == 1) { // check if scenario already has tag
		header("Location: ../data?scenarie=$scenario");
		exit;
	}

	$q = ("
		INSERT INTO tags (sce_id, tag, added_by_user_id)
		VALUES ($scenario, '" . dbesc($tag) . "', $user_id)
	");
	$r = doquery($q);
	if ($scetag_id = dbid($dblink) ) {
		$_SESSION['can_edit_tag'][$scetag_id] = TRUE;
		// award_achievement(91);
		chlog($scenario,'sce','Tag tilføjet: ' . $tag);
		award_achievement(100);
	}
} elseif ($action == 'delete') {
	if ( $_SESSION['user_editor'] || $_SESSION['user_admin'] || $_SESSION['can_edit_tag'][$tag_id] ) {
	$tag = getone("SELECT tag FROM tags WHERE id = $tag_id");
	doquery("DELETE FROM tags WHERE id = $tag_id");
	chlog($scenario,'sce','Tag fjernet: ' . $tag);
	}
} 

header("Location: ../data?scenarie=$scenario");
exit;
?>
