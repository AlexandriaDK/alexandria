<?php
// save page for users creating a game
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$token = $_REQUEST['token'] ?? '';
$user_id = $_SESSION['user_id'];
if (!$user_id) {
	header("Location: ../create");
	exit;
}

validatetoken( $token );

// valid user
$title = (string) $_REQUEST['title'];
$persons = (array) $_REQUEST['person'];
$begindate = (string) $_REQUEST['begindate'];
$enddate = (string) $_REQUEST['enddate'];
$location = (string) $_REQUEST['location'];
$website = (string) $_REQUEST['website'];
$description = (string) $_REQUEST['description'];
$notes = (string) $_REQUEST['notes'];
$larp = (int) (bool) ($_REQUEST['larp'] ?? FALSE);

$personlist = "";
foreach($persons AS $person) {
	if ($person) {
		$personlist .= $person['name'] . PHP_EOL;
	}
}

$internal = "Game created by user $user_id on " . date("Y-m-d H:i:s") . "\n\nPersons:\n$personlist\n\nUser notes:\n=====\n" . $notes . "\n";

if (!$title) {
	header("Location: ../create");
	exit;
}

$game = [
	'title' => $title,
	'persons' => $persons,
	'organizer' => $organizer,
	'gamesystem_id' => ($larp ? 73 : NULL),
	'descriptions' => ['en' => trim($description)],
	'urls' => [$website],
	'internal' => $internal
];
$game_id = create_game($game, $internal);

header("Location: ../data?scenarie=$game_id");
exit;
