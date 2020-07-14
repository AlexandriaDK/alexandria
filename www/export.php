<?php
$timestamp_start = date("c");
require("./connect.php");
require("base.inc.php");
$dataset = (string) ($_REQUEST['dataset'] ?? '');
$data_id = (int) ($_REQUEST['data_id'] ?? 0);
$output = [];

if ( $dataset === 'persons' ) {
	$data = getall( "SELECT id, firstname, surname FROM aut ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'games' ) {
	$data = getall( "SELECT id, title, boardgame, sys_id AS system_id, sys_ext AS system_extra, aut_extra AS person_extra, gms_min, gms_max, players_min, players_max, participants_extra FROM sce ORDER BY id", FALSE );
	$output = $data;
} elseif ( $dataset === 'conventions' ) {
	$data = getall( "SELECT a.id, a.name, a.year, a.begin, a.end, a.place, a.conset_id, a.description, a.confirmed, a.cancelled, a.country, b.name AS conset_name FROM convent a LEFT JOIN conset b ON a.conset_id = b.id ORDER BY a.id", FALSE);
	$output = $data;
} elseif ( $dataset === 'conventionsets' ) {
	$data = getall( "SELECT id, name, description, country FROM conset ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'systems' ) {
	$data = getall( "SELECT id, name, description FROM sys ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'genres' ) {
	$data = getall( "SELECT id, name, genre FROM gen ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'tags' ) {
	$data = getall( "SELECT id, tag, description FROM tag ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'gameruns' ) {
	$data = getall( "SELECT id, sce_id AS game_id, begin, end, location, description, cancelled FROM scerun ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'titles' ) {
	$data = getall( "SELECT id, title, title_label, priority, iconfile, iconwidth, iconheight, textsymbol FROM title ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'presentations' ) {
	$data = getall( "SELECT id, event, event_label, iconfile, textsymbol FROM pre ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'feeds' ) {
	$data = getall( "SELECT id, url, owner, aut_id AS person_id, name, pageurl, lastchecked, podcast, pauseupdate FROM feeds ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'trivia' ) {
	$data = getall( "SELECT id, data_id, category, fact FROM trivia ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'links' ) {
	$data = getall( "SELECT id, data_id, category, url, description FROM links ORDER BY id", FALSE);
	$output = $data;
} elseif ( $dataset === 'aliases' ) {
	$data = getall( "SELECT id, data_id, category, label, visible FROM alias WHERE visible = 1 ORDER BY category, data_id, id", FALSE); // Don't expose hidden aliases yet
	$output = $data;
} elseif ( $dataset === 'sitetexts' ) {
	$data = getall( "SELECT id, label, text, language, lastupdated FROM weblanguages ORDER BY language, label, id", FALSE);
	$output = $data;
} elseif ( $dataset === 'person_game_title_connections' ) {
	$data = getall( "SELECT id, aut_id AS person_id, sce_id AS game_id, tit_id AS title_id, note FROM asrel ORDER BY aut_id, sce_id, id", FALSE);
	$output = $data;
} elseif ( $dataset === 'game_convention_title_connections' ) {
	$data = getall( "SELECT id, sce_id AS game_id, convent_id AS convention_id, pre_id AS presentation_id FROM csrel ORDER BY convention_id, sce_id, id", FALSE);
	$output = $data;
} elseif ( $dataset === 'person_convention_connections' ) {
	$data = getall(" SELECT id, aut_id AS person_id, convent_id AS convention_id, aut_extra AS person_extra, role FROM acrel ORDER BY convention_id, aut_id, id", FALSE);
	$output = $data;
} elseif ( $dataset === 'sqlstructure' ) {
	$tables = [ 'aut', 'sce', 'convent', 'conset', 'sys', 'gen', 'tag', 'tags', 'scerun', 'title', 'pre', 'feeds', 'feedcontent', 'trivia', 'links', 'alias', 'weblanguages', 'asrel', 'csrel', 'acrel', 'users', 'userlog', 'news', 'files', 'filedata', 'filedownloads', 'awards', 'award_categories', 'award_nominee_entities', 'award_nominees', 'achievements', 'user_achievements', 'log', 'searches', 'installation' ];
	$tablecreate = [];
	foreach ( $tables AS $table ) {
		$create = getrow( "SHOW CREATE TABLE `$table`" );
		$tablecreate[ $table ] = $create[1];
	}
	$output = $tablecreate;
} elseif ( $dataset !== '' ) { // default
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
			'person_game_title_connections' => 'Relations between persons, games, and titles',
			'game_convention_title_connections' => 'Relations between games, conventions, and presentations',
			'person_convention_connections' => 'Relations between persons and conventions as organizers',
		],
		'examples' => [
			'export.php' => 'This overview',
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
