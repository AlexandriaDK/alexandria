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

validatetoken($token);

// valid user
$title = (string) $_REQUEST['title'];
$persons = (array) $_REQUEST['person'];
$runbegin = (string) $_REQUEST['runbegin'];
$runend = (string) $_REQUEST['runend'];
$runlocation = (string) $_REQUEST['runlocation'];
$rundescription = (string) $_REQUEST['rundescription'];
$website = (string) $_REQUEST['website'];
$description = (string) $_REQUEST['description'];
$notes = (string) $_REQUEST['notes'];
$gametype = (string) $_REQUEST['gametype'];
$useremail = (string) $_REQUEST['useremail'];

$personlist = "";
foreach ($persons as $person) {
	if ($person["name"]) {
		$personlist .= $person['name'] . PHP_EOL;
	}
}

$internal = "Game created by user $user_id on " . date("Y-m-d H:i:s") . "\n\nPersons:\n$personlist\n\n";
if ($useremail) {
	$internal .= "User e-mail: $useremail\n\n";
}
$internal .= "User notes:\n=====\n" . $notes . "\n";

$gamesystem_id = ($gametype == 'larp' ? 73 : NULL);
$boardgame = ($gametype == 'boardgame');

$runs = [
	[
		'begin' => $runbegin,
		'end' => $runend,
		'location' => $runlocation,
		'description' => $rundescription
	]
];

if (!$title) {
	header("Location: ../create");
	exit;
}

$game = [
	'title' => $title,
	'persons' => $persons,
	'organizer' => $organizer,
	'gamesystem_id' => $gamesystem_id,
	'descriptions' => ['en' => trim($description)],
	'urls' => [$website],
	'runs' => $runs,
	'boardgame' => $boardgame,
	'internal' => $internal
];
$game_id = create_game($game, $internal);

header("Location: ../data?scenarie=$game_id");
exit;
