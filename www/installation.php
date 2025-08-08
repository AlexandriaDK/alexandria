<?php
// from this point on we know that the setup is incomplete
define('IMPORT_ENDPOINT', 'https://alexandria.dk/en/export');
define('INSTALLATION_DEBUG', FALSE);
$action = $_POST['action'] ?? '';
if ($action && ($_SESSION['token'] !== $_POST['token'])) {
  $t->assign('stage', 'tokenerror');
  $t->assign('installation', TRUE);
  $t->assign('dbname', DB_NAME);
  $t->display('installation.tpl');
  exit;
}

function dbmultiinsert($table, $allvalues, $fields = NULL)
{
  // Special case for spatial data
  if ($table == 'locations') {
    $fields = ['id', 'name', 'address', 'city', 'country', 'note', 'geo'];
  }
  if ($fields == NULL) {
    $fields = [];
    foreach ($allvalues[0] as $key => $list) {
      $fields[] = $key;
    }
  }
  $dataset = $datasets = [];
  foreach ($allvalues as $list) {
    $set = [];
    foreach ($list as $key => $part) {
      if ($table == 'locations' && $key == 'latitude') {
        $latitude = $longitude = NULL;
        $latitude = $part;
        continue;
      } elseif ($table == 'locations' && $key == 'longitude') {
        $longitude = $part;
        if ($latitude && $longitude) {
          $set[] = "ST_GeomFromText('POINT($latitude $longitude)', 4326)";
        } else {
          $set[] = 'NULL';
        }
      } else {
        $set[] = (is_null($part) ? 'NULL' : (is_numeric($part) ? $part : "'" . dbesc($part) . "'"));
      }
    }
    $dataset[] = "(" . implode(", ", $set) . ")";
    if (count($dataset) >= 1000) {
      $datasets[] = $dataset;
      $dataset = [];
    }
  }
  if ($dataset) {
    $datasets[] = $dataset;
  }

  if ($datasets) {
    if (INSTALLATION_DEBUG === TRUE) {
      print "<pre>";
      print "TABLE $table \n";
    }
    doquery("DELETE FROM `$table` ");
    foreach ($datasets as $dataset) {
      $multisql = "INSERT INTO `$table` (" . implode(", ", $fields) . ") VALUES " . implode(", ", $dataset);
      if (INSTALLATION_DEBUG === TRUE) {
        print htmlspecialchars($multisql) . "\n";
      }
      doquery($multisql);
      if (INSTALLATION_DEBUG === TRUE) {
        $error = dberror();
        if ($error) {
          print "\nMySQL error: " . $error . "\n";
        }
      }
    }
    return true;
  } else {
    return false;
  }
}

if (!defined("INSTALLNOW") || INSTALLNOW !== TRUE) { //should not be called directly
  header("HTTP/1.1 403 Forbidden");
  header("X-Error: Setup");

  die("Do not access this file directly. Just visit the front page.");
  exit;
}
header("HTTP/1.1 503 Service Unavailable");
header("X-Error: Setup");
if (!isset($_SESSION['token'])) {
  $_SESSION['token'] = md5(uniqid());
}
$t->assign('token', $_SESSION['token'] ?? '');

if ($action == 'importstructure') {
  $url = IMPORT_ENDPOINT . '?setup=sqlstructure';
  $sqltables = json_decode(file_get_contents($url));
  if (!$sqltables) {
    $t->assign('stage', 'dbsetupnodata');
  } else {
    doquery("SET foreign_key_checks = 0");
    foreach ($sqltables->result as $table => $sqlstatement) {
      if (INSTALLATION_DEBUG === TRUE) {
        print "Creating table $table\n";
      }
      doquery("DROP TABLE IF EXISTS `$table`");
      doquery($sqlstatement);
    }
    doquery("SET foreign_key_checks = 1");
    if (getone("SHOW tables LIKE 'installation'") !== NULL) {
      doquery("INSERT INTO `installation` (`key`, `value`) VALUES ('status', 'empty')");
    }
    header("Location: ./");
    exit;
  }
} elseif ($action == 'populate') {
  $url = IMPORT_ENDPOINT;
  $datasets = json_decode(file_get_contents($url));
  doquery("SET foreign_key_checks = 0");
  foreach ($datasets->result->datasets as $dataset => $description) {
    if ($dataset == 'all') { // Don't fetch all in one result; request individually and skip special case for "all" 
      continue;
    }
    $url = IMPORT_ENDPOINT . "?dataset=" . rawurlencode($dataset);
    doquery("DELETE FROM installation WHERE `key` = 'currentdataset'");
    doquery("INSERT INTO installation (`key`, `value`) VALUES ('currentdataset', '" . dbesc($dataset) . "')");
    $data = json_decode(file_get_contents($url));

    switch ($dataset) {
      case 'persons':
      case 'games':
      case 'conventions':
      case 'conventionsets':
      case 'systems':
      case 'genres':
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
      case 'sitetexts':
      case 'files':
      case 'awards':
      case 'award_categories':
      case 'award_nominee_entities':
      case 'award_nominees':
      case 'magazines':
      case 'issues':
      case 'articles':
      case 'contributors':
      case 'article_reference':
      case 'person_game_title_relations':
      case 'game_convention_presentation_relations':
      case 'person_convention_relations':
      case 'genre_game_relations':
      case 'locations':
      case 'location_reference':
        $tablemap = ['persons' => 'person', 'conventions' => 'convention', 'conventionsets' => 'conset', 'systems' => 'gamesystem', 'genres' => 'genre', 'gameruns' => 'gamerun', 'titles' => 'title', 'presentations' => 'presentation', 'aliases' => 'alias', 'sitetexts' => 'weblanguages', 'tags' => 'tag', 'gametags' => 'tags', 'gamedescriptions' => 'game_description', 'magazines' => 'magazine', 'issues' => 'issue', 'articles' => 'article', 'contributors' => 'contributor', 'person_game_title_relations' => 'pgrel', 'game_convention_presentation_relations' => 'cgrel', 'person_convention_relations' => 'pcrel', 'genre_game_relations' => 'ggrel', 'games' => 'game', 'gametags' => 'tags', 'location_reference' => 'lrel'];
        if (isset($tablemap[$dataset])) {
          $table = $tablemap[$dataset];
        } else {
          $table = $dataset;
        }
        dbmultiinsert($table, $data->result);
        break;
      default:
        print "Unknown table from Alexandria server: $dataset";
        exit;
    }
  }
  doquery("SET foreign_key_checks = 1");
  doquery("DELETE FROM installation WHERE `key` = 'status'");
  doquery("INSERT INTO installation (`key`, `value`) VALUES ('status', 'ready')");
  header("Location: ./");
  exit;
} elseif ($action == 'activate') {
  doquery("DELETE FROM installation WHERE `key` = 'status'");
  doquery("INSERT INTO installation (`key`, `value`) VALUES ('status', 'live')");
  header("Location: ./");
  exit;
} elseif (getone("SHOW tables LIKE 'installation'") !== NULL) {
  if (getone("SELECT 1 FROM installation WHERE `key` = 'status' AND `value` = 'empty'")) {
    $t->assign('stage', 'populate');
  } elseif (getone("SELECT 1 FROM installation WHERE `key` = 'status' AND `value` = 'ready'")) {
    $t->assign('stage', 'ready');
  }
} else {
  $t->assign('stage', 'dbsetup');
}

$t->assign('installation', TRUE);
$t->assign('dbname', DB_NAME);
$t->assign('pagetitle', 'Installation');
$t->display('installation.tpl');

exit;
