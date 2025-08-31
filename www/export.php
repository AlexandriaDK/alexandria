<?php
header("Access-Control-Allow-Origin: *");
$timestamp_start = date("c");
require("./connect.php");
require("base.inc.php");
$dataset = (string) ($_REQUEST['dataset'] ?? '');
$setup = (string) ($_REQUEST['setup'] ?? '');
$newestversion = (string) ($_REQUEST['newestversion'] ?? '');
$data_id = (int) ($_REQUEST['data_id'] ?? 0);
$output = [];

$exportqueries = [
  'persons' => "SELECT id, firstname, surname FROM person ORDER BY id",
  'games' => "SELECT id, title, boardgame, gamesystem_id, gamesystem_extra, person_extra, gms_min, gms_max, players_min, players_max, participants_extra FROM game ORDER BY id",
  'conventions' => "SELECT a.id, a.name, a.year, a.begin, a.end, a.place, a.conset_id, a.description, a.confirmed, a.cancelled, a.country FROM convention a ORDER BY a.id",
  'conventionsets' => "SELECT id, name, description, country FROM conset ORDER BY id",
  'systems' => "SELECT id, name, description FROM gamesystem ORDER BY id",
  'genres' => "SELECT id, name, genre FROM genre ORDER BY id",
  'genre_game_relations' => "SELECT id, genre_id, game_id FROM ggrel ORDER BY game_id, genre_id, id",
  'tags' => "SELECT id, tag, description, '' AS internal FROM tag ORDER BY id",
  'gametags' => "SELECT id, game_id, tag FROM tags ORDER BY id",
  'gameruns' => "SELECT id, game_id, begin, end, location, description, cancelled, country FROM gamerun ORDER BY id",
  'gamedescriptions' => "SELECT id, game_id, description, language, note FROM game_description ORDER BY game_id, language, id",
  'titles' => "SELECT id, title, title_label, priority, iconfile, iconwidth, iconheight, textsymbol FROM title ORDER BY id",
  'presentations' => "SELECT id, event, event_label, iconfile, textsymbol FROM presentation ORDER BY id",
  'feeds' => "SELECT id, url, owner, person_id, name, pageurl, lastchecked, podcast, pauseupdate FROM feeds ORDER BY id",
  'trivia' => "SELECT id, fact, '' AS internal, person_id, game_id, convention_id, conset_id, gamesystem_id, tag_id, fact FROM trivia ORDER BY id",
  'links' => "SELECT id, url, description, person_id, game_id, convention_id, conset_id, gamesystem_id, tag_id FROM links ORDER BY id",
  'aliases' => "SELECT id, label, visible, language, person_id, game_id, convention_id, conset_id, gamesystem_id FROM alias WHERE visible = 1 ORDER BY id", // Don't expose hidden aliases yet
  'sitetexts' => "SELECT id, label, text, language, lastupdated FROM weblanguages ORDER BY language, id",
  'awards' => "SELECT id, name, conset_id, description, label FROM awards ORDER BY id",
  'award_categories' => "SELECT id, name, convention_id, description, award_id FROM award_categories ORDER BY id",
  'award_nominee_entities' => "SELECT id, award_nominee_id, label, person_id, game_id FROM award_nominee_entities ORDER BY award_nominee_id, id",
  'award_nominees' => "SELECT id, award_category_id, game_id, name, nominationtext, winner, ranking FROM award_nominees ORDER BY id",
  'person_game_title_relations' => "SELECT id, person_id, game_id, title_id, convention_id, gamerun_id, note FROM pgrel ORDER BY person_id, game_id, id",
  'game_convention_presentation_relations' => "SELECT id, game_id, convention_id, presentation_id FROM cgrel ORDER BY convention_id, game_id, id",
  'person_convention_relations' => "SELECT id, person_id, convention_id, person_extra, role FROM pcrel ORDER BY convention_id, person_id, id",
  'magazines' => "SELECT id, name, description FROM magazine ORDER BY id",
  'issues' => "SELECT id, magazine_id, title, releasedate, releasetext FROM issue ORDER BY magazine_id, releasedate, id",
  'articles' => "SELECT id, issue_id, page, title, description, articletype, game_id FROM article ORDER BY issue_id, id",
  'contributors' => "SELECT id, person_id, person_extra, role, article_id FROM contributor ORDER BY id",
  'article_reference' => "SELECT id, article_id, person_id, game_id, convention_id, conset_id, gamesystem_id, tag_id, magazine_id, issue_id FROM article_reference ORDER BY id",
  'locations' => "SELECT id, name, address, city, country, note, ST_X(geo) AS latitude, ST_Y(geo) AS longitude FROM locations ORDER BY id",
  'location_reference' => "SELECT id, location_id, convention_id, gamerun_id FROM lrel ORDER BY id",
  'files' => "SELECT id, filename, description, downloadable, inserted, language, indexed, game_id, convention_id, conset_id, gamesystem_id, tag_id, issue_id FROM files WHERE downloadable = 1 ORDER BY id",
];

if ($dataset) {
  switch ($dataset) {
    case 'persons':
    case 'systems':
    case 'games':
    case 'conventionsets':
    case 'conventions':
    case 'genres':
    case 'genre_game_relations':
    case 'tags':
    case 'gametags':
    case 'gameruns':
    case 'gamedescriptions':
    case 'titles':
    case 'presentations':
    case 'feeds':
    case 'trivia':
    case 'links':
    case 'aliases':
    case 'files':
    case 'sitetexts':
    case 'awards':
    case 'award_categories':
    case 'award_nominee_entities':
    case 'award_nominees':
    case 'person_game_title_relations':
    case 'game_convention_presentation_relations':
    case 'person_convention_relations':
    case 'magazines':
    case 'issues':
    case 'articles':
    case 'contributors':
    case 'article_reference':
    case 'locations':
    case 'location_reference':
      $output = getall($exportqueries[$dataset], false);
      break;
    case 'all':
      $output = [];
      foreach ($exportqueries as $table => $query) {
        $output[$table] = getall($query, false);
      }
      break;
    default:
      $data = [
        "error" => "Unknown dataset"
      ];
      $output = $data;
  }
} elseif ($setup === 'sqlstructure') { // Order is important due to foreign keys
  $tables = ['person', 'gamesystem', 'game', 'conset', 'convention', 'genre', 'ggrel', 'tag', 'tags', 'gamerun', 'title', 'presentation', 'game_description', 'feeds', 'feedcontent', 'trivia', 'links', 'alias', 'weblanguages', 'pgrel', 'cgrel', 'pcrel', 'users', 'loginmap', 'userlog', 'news', 'awards', 'award_categories', 'award_nominee_entities', 'award_nominees', 'achievements', 'user_achievements', 'log', 'searches', 'updates', 'filedownloads', 'installation', 'magazine', 'issue', 'article', 'contributor', 'article_reference', 'rpgforum_posts', 'files', 'filedata', 'locations', 'lrel'];
  $tablecreate = [];
  foreach ($tables as $table) {
    $create = getrow("SHOW CREATE TABLE `$table`");
    $tablecreate[$table] = $create[1];
  }
  $output = $tablecreate;
} elseif ($newestversion) {
  switch ($newestversion) {
    case 'powershellupdater':
      $output = ['version' => 1.6020250514];
      break;

    default:
      $output = ['error' => 'Unknown software'];
  }
} elseif ($dataset !== '') { // unknown dataset
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
    'datasets' => [ // Order is important due to foreign keys
      'all' => 'All datasets combined (about 15 MB!)',
      'persons' => 'Persons in the Alexandria database',
      'systems' => 'Role-playing systems',
      'games' => 'Games, including role-playing scenarios, designed board games, and LARPs',
      'conventionsets' => 'Sets of gaming conventions',
      'conventions' => 'Gaming conventions',
      'genres' => 'Genres for games',
      'tags' => 'Tag descriptions',
      'gametags' => 'Relations between tags and games',
      'gameruns' => 'Individual runs of games outside of conventions',
      'gamedescriptions' => 'Descriptions and presentations of games in multitude of languages',
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
      'person_game_title_relations' => 'Relations between persons, games, and titles',
      'game_convention_presentation_relations' => 'Relations between games, conventions, and presentations',
      'genre_game_relations' => 'Relations between games and genres',
      'person_convention_relations' => 'Relations between persons and conventions as organizers',
      'magazines' => 'Magazines and club folders',
      'issues' => 'Issues for magazines',
      'articles' => 'Articles in issues',
      'contributors' => 'Contributors for magazines',
      'article_reference' => 'References in articles to other data entries',
      'locations' => 'Geographical locations, used by conventions and game runs',
      'location_reference' => 'References in locations to conventions and game runs',
      'files' => 'List of files for scenarios, convents and convention sets at alexandria.dk',
    ],
    'examples' => [
      'export' => 'This overview',
      'export?setup=sqlstructure' => 'Get SQL structure for tables',
      'export?dataset=persons' => 'Get all persons',
      //			'export?dataset=persons&data_id=1' => 'Get person with data id 1',
      //			'export?dataset=game&data_id=4,7' => 'Get scenarios with data id 4 and 7'
    ],
    'newestversion' => [
      'powershellupdater' => 'Get latest version number of Powershell Updater for offline arhchive'
    ]
  ];
  $output = $datasets;
}

$timestamp_end = date("c");

$output = [
  'result' => $output,
  'request' => ["received" => $timestamp_start, "finished" => $timestamp_end],
  'license' => 'The database is owned by Alexandria.dk and protected by the database rights in Danish law of Copyright ("Ophavsretsloven", § 71). You are allowed to use the API and the data for *non-commercial* purposes. Alexandria.dk must be credited, if possible with a link. Game files are not available through this API.',
  'access' => 'Access to this API does not require_once login, tokens or other authentication mechanisms. Access can be restricted for various reasons, e.g. if the server is overloaded or if too many requests are sent in a short time.',
  'status' => 'ready'
];

$json_output = json_encode($output);
header("Content-Type: application/json");
header("Content-Length: " . strlen($json_output));
print $json_output;
