<?php
$timestamp_start = date("c");
require("./connect.php");
require("base.inc.php");
$dataset = (string) ($_REQUEST['dataset'] ?? '');
$setup = (string) ($_REQUEST['setup'] ?? '');
$data_id = (int) ($_REQUEST['data_id'] ?? 0);
$output = [];

if ( $dataset ) {
	switch ( $dataset ) {
	case 'persons':
		$output = getall( "SELECT id, firstname, surname FROM aut ORDER BY id", FALSE);
		break;
	case 'games':
		$output = getall( "SELECT id, title, boardgame, sys_id AS system_id, sys_ext AS system_extra, aut_extra AS person_extra, gms_min, gms_max, players_min, players_max, participants_extra FROM sce ORDER BY id", FALSE );
		break;
	case 'conventions':
		$output = getall( "SELECT a.id, a.name, a.year, a.begin, a.end, a.place, a.conset_id, a.description, a.confirmed, a.cancelled, a.country FROM convent a ORDER BY a.id", FALSE);
		break;
	case 'conventionsets':
		$output = getall( "SELECT id, name, description, country FROM conset ORDER BY id", FALSE);
		break;
	case 'systems':
		$output = getall( "SELECT id, name, description FROM sys ORDER BY id", FALSE);
		break;
	case 'genres':
		$output = getall( "SELECT id, name, genre FROM gen ORDER BY id", FALSE);
		break;
	case 'tags':
		$output = getall( "SELECT id, tag, description FROM tag ORDER BY id", FALSE);
		break;
	case 'gametags':
		$output = getall( "SELECT id, sce_id AS game_id, tag FROM tags ORDER BY id", FALSE);
		break;
	case 'gameruns':
		$output = getall( "SELECT id, sce_id AS game_id, begin, end, location, description, cancelled FROM scerun ORDER BY id", FALSE);
		break;
	case 'titles':
		$output = getall( "SELECT id, title, title_label, priority, iconfile, iconwidth, iconheight, textsymbol FROM title ORDER BY id", FALSE);
		break;
	case 'presentations':
		$output = getall( "SELECT id, event, event_label, iconfile, textsymbol FROM pre ORDER BY id", FALSE);
		break;
	case 'feeds':
		$output = getall( "SELECT id, url, owner, aut_id AS person_id, name, pageurl, lastchecked, podcast, pauseupdate FROM feeds ORDER BY id", FALSE);
		break;
	case 'trivia':
		$output = getall( "SELECT id, data_id, category, fact FROM trivia ORDER BY id", FALSE);
		break;
	case 'links':
		$output = getall( "SELECT id, data_id, category, url, description FROM links ORDER BY id", FALSE);
		break;
	case 'aliases':
		$output = getall( "SELECT id, data_id, category, label, visible FROM alias WHERE visible = 1 ORDER BY category, data_id, id", FALSE); // Don't expose hidden aliases yet
		break;
	case 'sitetexts':
		$output = getall( "SELECT id, label, text, language, lastupdated FROM weblanguages ORDER BY language, label, id", FALSE);
		break;
	case 'awards':
		$output = getall( "SELECT id, name, conset_id, description, label FROM awards ORDER BY id", FALSE);
		break;
	case 'award_categories':
		$output = getall( "SELECT id, name, convent_id, description, award_id FROM award_categories ORDER BY id", FALSE);
		break;
	case 'award_nominee_entities':
		$output = getall( "SELECT id, award_nominee_id, data_id, category, label FROM award_nominee_entities ORDER BY award_nominee_id, id", FALSE);
		break;
	case 'award_nominees':
		$output = getall( "SELECT id, award_category_id, sce_id, name, nominationtext, winner, ranking FROM award_nominees ORDER BY id", FALSE);
		break;
	case 'person_game_title_connections':
		$output = getall( "SELECT id, aut_id AS person_id, sce_id AS game_id, tit_id AS title_id, note FROM asrel ORDER BY aut_id, sce_id, id", FALSE);
		break;
	case 'game_convention_title_connections':
		$output = getall( "SELECT id, sce_id AS game_id, convent_id AS convention_id, pre_id AS presentation_id FROM csrel ORDER BY convention_id, sce_id, id", FALSE);
		break;
	case 'person_convention_connections':
		$output = getall(" SELECT id, aut_id AS person_id, convent_id AS convention_id, aut_extra AS person_extra, role FROM acrel ORDER BY convention_id, aut_id, id", FALSE);
		break;
	}
} elseif ( $setup === 'sqlstructure' ) {
	$tables = [ 'aut', 'sce', 'convent', 'conset', 'sys', 'gen', 'tag', 'tags', 'scerun', 'title', 'pre', 'feeds', 'feedcontent', 'trivia', 'links', 'alias', 'weblanguages', 'asrel', 'csrel', 'acrel', 'users', 'userlog', 'news', 'files', 'filedata', 'filedownloads', 'awards', 'award_categories', 'award_nominee_entities', 'award_nominees', 'achievements', 'user_achievements', 'log', 'searches', 'installation' ];
	$tablecreate = [];
	foreach ( $tables AS $table ) {
		$create = getrow( "SHOW CREATE TABLE `$table`" );
		$tablecreate[ $table ] = $create[1];
	}
	$output = $tablecreate;
} elseif ( $dataset !== '' ) { // unknown dataset
	$data = [
		"error" => "Unknown dataset"
	];
	$output = $data;
} else {
	$datasets = [
		'info' => 'This is the export resource for all public Alexandria.dk gaming data. Export is JSON formatted.',
		'setup' => [
			'sqlstructure' => 'MySQL structure for all necessary tables'
		],
		'datasets' => [
			'persons' => 'Persons in the Alexandria database',
			'games' => 'Games, including role-playing scenarios, designed board games, and LARPs',
			'conventions' => 'Gaming conventions',
			'conventionsets' => 'Sets of gaming conventions',
			'systems' => 'Role-playing systems',
			'genres' => 'Genres for games',
			'tags' => 'Tags for games',
			'gameruns' => 'Individual runs of games outside of conventions',
			'titles' => 'Person titles in relation to games',
			'presentations' => 'Presentation data in relation to conventions',
			'feeds' => 'Sites with RSS feeds for syndication',
			'trivia' => 'Trivia for persons, games, conventions, convention sets, systems and tags',
			'links' => 'Links for persons, games, conventions, convention sets, systems and tags',
			'aliases' => 'Aliases for persons, games, conventions, convention sets, and systems',
			'sitetexts' => 'Site texts in different languages',
			'awards' => 'Types of awards',
			'award_categories' => 'Individual awards',
			'award_nominee_entities' => 'Persons connected to an award nomination',
			'award_nominees' => 'Nominated persons or otherwise for an award',
			'person_game_title_connections' => 'Relations between persons, games, and titles',
			'game_convention_title_connections' => 'Relations between games, conventions, and presentations',
			'person_convention_connections' => 'Relations between persons and conventions as organizers',
		],
		'examples' => [
			'export.php' => 'This overview',
			'export.php?setup=structure' => 'Get SQL structure for tables',
			'export.php?dataset=persons' => 'Get all persons',
//			'export.php?dataset=persons&data_id=1' => 'Get person with data id 1',
			//		'export.php?dataset=game&data_id=4,7' => 'Get scenarios with data id 4 and 7'
		]
	];
	$output = $datasets;
}

$timestamp_end = date("c");

$output = [
	'result' => $output,
	'timestamps' => [ "received" => $timestamp_start, "finished" => $timestamp_end ],
	'export' => 'ready'
];
header("Content-Type: application/json");
print json_encode( $output );

?>
