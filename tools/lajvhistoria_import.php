<?php
// Import LARP data from lajvhistoria.se
require __DIR__ . "/../www/rpgconnect.inc.php";
require __DIR__ . "/../www/base.inc.php";

$url = 'https://lajvhistoria.se/export.php';
$localfile = __DIR__ . '/lajvhistoria_export.json';

function chlog($data_id, $category, $note = "")
{
  $user = 'Peter Brodersen';
  $authuserid = 4;
  $data_id = ($data_id == NULL ? 'NULL' : (int) $data_id);
  $note = dbesc($note);
  $query = "INSERT INTO log (data_id,category,time,user,user_id,note) " .
    "VALUES ($data_id,'$category',NOW(),'$user','$authuserid','$note')";
  $result = doquery($query);
  return $result;
}

function get_create_person($name, $lajv_id)
{
  preg_match('_(.*) (.*)_', $name, $names);
  $person_id = getone("SELECT id FROM person WHERE CONCAT(firstname, ' ', surname) = '" . dbesc($name) . "'");
  if (!$person_id) {
    $internal = "Autoimport from lajvhistoria by PB" . PHP_EOL . "lajvhistoria_id: $lajv_id" . PHP_EOL;
    $sql = "INSERT INTO person (firstname, surname, internal) VALUES ('" . dbesc($names[1]) . "', '" . dbesc($names[2]) . "', '" . dbesc($internal) . "')";
    $person_id = doquery($sql);
    chlog($person_id, 'person', 'Person created');
  }
  return $person_id;
}

function cleanname($name)
{
  $name = trim($name);
  $name = str_replace(' mfl.', '', $name);
  return $name;
}

function create_game($game, $persons, $organizations, $multiple_runs = FALSE, $existing_game_id = FALSE)
{
  $genre_lajv_alexandria_map = [
    1 => 3,
    4 => 8,
    9 => 4,
    19 => 5
  ];
  $lajv_id = (int) $game->id;
  $title = trim($game->name);
  $year = trim($game->year);
  $location = trim($game->location);
  $description = trim($game->notes);
  $gamesystem_id = 73; // LARP
  $person_extra = implode(", ", $organizations);
  $genres = [];
  $tags = [];
  $person_ids = [];
  foreach ($persons as $person) {
    $person_ids[] = get_create_person($person, $lajv_id);
  }
  $internal  = "";
  $internal .= "Autoimport from lajvhistoria by PB" . PHP_EOL;
  $internal .= "lajvhistoria_id: " . $lajv_id . PHP_EOL;
  $internal .= json_encode($game) . PHP_EOL . PHP_EOL;

  foreach ((array) $game->genres as $genre) {
    $gid = $genre_lajv_alexandria_map[$genre->id] ?? NULL;
    if ($gid) {
      $genres[] = $gid;
    } else {
      $tags[] = $genre->genre;
    }
  }

  if (! $existing_game_id) {
    // insert game
    $game_id_sql = "INSERT INTO game (title, internal, gamesystem_id, person_extra, rlyeh_id, boardgame) " .
      "VALUES ('" . dbesc($title) . "', '" . dbesc($internal) . "', $gamesystem_id, '" . dbesc($person_extra) . "', 0, 0)";
    $game_id = doquery($game_id_sql);
    chlog($game_id, 'sce', 'Game created');
  } else {
    $existing_internal = (string) getone("SELECT internal FROM game WHERE id = $existing_game_id");
    $internal = $internal . $existing_internal;
    doquery("UPDATE game SET internal = '" . dbesc($internal) . "' WHERE id = $existing_game_id");
    $game_id = $existing_game_id;
  }

  if ($description) {
    $language = 'sv';
    if ($multiple_runs || $existing_game_id) {
      $language .= " ($year)";
    }
    $desc_sql = "INSERT INTO game_description (game_id, description, language) VALUES ($game_id, '" . dbesc($description) . "', '$language')";
    doquery($desc_sql);
  }

  if ($year) {
    $begin = $end = $year . '-00-00';
    $run_sql = "INSERT INTO gamerun (game_id, begin, end, location, country) VALUES ($game_id, '$begin', '$end', '" . dbesc($location) . "', 'se')";
    doquery($run_sql);
  }

  foreach ($person_ids as $pid) {
    if ($multiple_runs || $existing_game_id) {
      $assql = "INSERT INTO pgrel (person_id, game_id, title_id, note) VALUES ($pid, $game_id, 4, '$year run')";
    } else {
      $assql = "INSERT INTO pgrel (person_id, game_id, title_id) VALUES ($pid, $game_id, 4)";
    }
    doquery($assql);
  }
  foreach ($genres as $gid) {
    if (! getone("SELECT 1 FROM ggrel WHERE genre_Id = $gid AND game_id = $game_id")) {
      $gsql = "INSERT INTO ggrel (genre_Id, game_id) VALUES ($gid, $game_id)";
      doquery($gsql);
    }
  }
  foreach ($tags as $tag) {
    if (! getone("SELECT 1 FROM tags WHERE game_id = $game_id AND tag = '" . dbesc($tag) . "'")) {
      $gsql = "INSERT INTO tags (game_id, tag) VALUES ($game_id, '" . dbesc($tag) . "')";
      doquery($gsql);
    }
  }

  $url = 'https://lajvhistoria.se/lajv/' . $lajv_id;
  if (! getone("SELECT 1 FROM links WHERE category = 'sce' AND data_id = $game_id AND url = '" . dbesc($url) . "'")) {
    $lsql = "INSERT INTO links (category, data_id, url, description) VALUES ('sce', $game_id, '" . dbesc($url) . "', '{\$_sce_file_scenario}, Lajvhistoria.se')";
    doquery($lsql);
  }
}

function import_games($games)
{
  $alexandria_lajvhistoria_user_id = 21;
  $known_organizations = get_known_organizations();
  $orgmap = get_orgmap();

  $titlecount = [];
  $multiple_run_titles = [];
  foreach ($games as $game) { // Pre-run, check for non-unique titles
    $title = trim($game->name);
    if (in_array($title, $titlecount)) { // not fastest way; doesn't scale; should use key
      $multiple_run_titles[] = $title; // Might create duplicates, this is okay
    } else {
      $titlecount[] = $title;
    }
  }
  foreach ($games as $game) {
    if ($game->added_by == $alexandria_lajvhistoria_user_id) { // skip if source is Alexandria
      continue;
    }
    $title = trim($game->name);
    $multiple = in_array($title, $multiple_run_titles);
    $lajv_id = (int) $game->id;
    $aid = getone("SELECT id FROM game WHERE title = '" . dbesc($title) . "'");
    if ($aid) {
      // :TODO: Insert link into new data table
      // For now, update the internal note
      $internal = (string) getone("SELECT internal FROM game WHERE id = $aid");
      $pattern = '/^lajvhistoria_id: ' . $lajv_id . '(?!\d)/m';
      if (preg_match($pattern, $internal)) {  // skip if we already "know" the scenario
        print "Skipping $title ($aid)" . PHP_EOL;
        continue;
      } else {
        print "Updating $title ($aid)" . PHP_EOL;
      }
    } else {
      print "Creating $title" . PHP_EOL;
    }
    $names = [];
    $organizations = [];
    $persons = [];
    $org = (string) $game->org;
    $org = trim($org);
    if (isset($orgmap[$org])) {
      if (isset($orgmap[$org]['organization'])) {
        $organizations[] = $orgmap[$org]['organization'];
      }
      $names = $orgmap[$org]['person'];
    } elseif (preg_match('/[\(\)]/', $org)) {
      $organizations[] = $org;
    } else {
      $names = explode(", ", $org);
    }

    foreach ($names as $name) {
      $name = cleanname($name);
      if (in_array($name, $known_organizations) || (strpos($name, " ", 0) === FALSE)) {
        $organizations[] = $name;
      } else {
        if (isset($orgmap[$name])) {
          if (isset($orgmap[$name]['organization'])) {
            $organizations[] = $orgmap[$name]['organization'];
          }
          $persons = array_merge($persons, $orgmap[$name]['person']);
        } else {
          $persons[] = $name;
        }
      }
    }
    create_game($game, $persons, $organizations, $multiple, $aid);
  }
}

function get_known_organizations()
{
  $known_organizations = [
    'ALF',
    'Alternaliv',
    'Ariadnes Röda Tråd',
    'Arrangörsföreningen Mollberg',
    'Arrangörsgruppen Fenix',
    'Atropos',
    'Avalon',
    'Avesta Bjurfors RollspelsFörening',
    'Bardo',
    'Berättelsefrämjandet',
    'Drakontia Lajvsällskap',
    'Eldar',
    'Enhörningen',
    'Ett Glas',
    'FF International',
    'Fabel',
    'Fågel Fenris',
    'Förbundet Vi Unga',
    'Föreningen Solnedgång',
    'Galadrim',
    'Gyllene Hjorten',
    'Guranga',
    'Halvhörningen',
    'HSRF',
    'Interaktiva Uppsättningar',
    'Illmyhr',
    'Ithil Calen',
    'Kindred Society',
    'Krok',
    'Lajvsällskapet Romantiska Sagor',
    'MASK',
    'Militärhistoriska Sällskapet',
    'Morgonstjärna',
    'Nebulosus',
    'Nocke Ting',
    'Oroboros',
    'ORoK',
    'Roverscoutlaget Orion',
    'RSK Härskarringen',
    'Riksteatern',
    'Scoutkår i Falun',
    'Skellefteå Lajvförening',
    'Skellefteås Lajvförening',
    'Sollentunagruppen',
    'Stormens Öga',
    'Särimners Vänner',
    'Teater K',
    'Täby Spelsällskap',
    'The Story Lab',
    'Ulricehamns Liveförening',
    'V.Ä.S',
    'Wyrd',
    'Zedalia',
  ];
  return $known_organizations;
}

function get_orgmap()
{
  $orgmap = [
    'Daniel Westberg (Chilla)' => ['person' => ['Daniel Westberg']],
    'Sollentunagruppen, Daniel Westberg (Chilla), Filip Hofman (Eriksson), Gustaf af Geijerstam' => ['person' => ['Daniel Westberg', 'Filip Hofman (Eriksson)', 'Gustaf af Geijerstam'], 'organization' => 'Sollentunagruppen'],
  ];
  return $orgmap;
}
$file = $url;

$games = json_decode(file_get_contents($file));
if (! $games) {
  die("Can't load games" . PHP_EOL);
}

doquery("START TRANSACTION");
import_games($games);
doquery("COMMIT");
