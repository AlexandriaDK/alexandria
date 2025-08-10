<?php
ini_set('memory_limit', '1G');
set_time_limit(0);

// --- Configuration ---
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'alexuser');
define('DB_PASS', getenv('DB_PASS') ?: 'alexpass');
define('DB_NAME', getenv('DB_NAME') ?: 'alexandria');
define('ALEXANDRIA_URL', getenv('ALEXANDRIA_URL') ?: 'https://alexandria.dk/en/export?dataset=all');
define('RSS_URL', getenv('RSS_URL') ?: 'https://alexandria.dk/rss.php');

// --- Utility Functions ---

define('PREPARE_FAILED_MSG', 'Prepare failed: ');
function logMsg($msg, $isError = false)
{
  $prefix = $isError ? '[ERROR] ' : '[INFO] ';
  fwrite($isError ? STDERR : STDOUT, $prefix . $msg . "\n");
}

function exitWithError($msg, $code = 1)
{
  logMsg($msg, true);
  exit($code);
}

function waitForMysql($host, $user, $pass, $db, $maxTries = 60, $sleepSec = 2)
{
  for ($i = 0; $i < $maxTries; $i++) {
    $mysqli = @new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_errno === 0) {
      return $mysqli;
    }
    logMsg("Waiting for MySQL...");
    sleep($sleepSec);
  }
  exitWithError("MySQL not ready after " . ($maxTries * $sleepSec / 60) . " minutes, exiting.");
}

function fetchJson($url)
{
  logMsg("Downloading data from $url");
  $json = @file_get_contents($url);
  if ($json === false) {
    exitWithError("Failed to download JSON from $url");
  }
  $data = json_decode($json, true);
  if (!$data || !isset($data['result'])) {
    exitWithError("Invalid JSON from $url");
  }
  return $data['result'];
}

function fetchRss($url)
{
  logMsg("Downloading RSS from $url");
  $rss = @file_get_contents($url);
  if ($rss === false) {
    logMsg("Failed to download RSS from $url. Skipping news import.", true);
    return null;
  }
  return $rss;
}

function parseRssDate($date_str)
{
  $parts = explode(' ', $date_str);
  if (count($parts) >= 5) {
    $date_part = implode(' ', array_slice($parts, 1, 4));
    $dt = DateTime::createFromFormat('d M Y H:i:s', $date_part);
    if ($dt) {
      return $dt->format('Y-m-d H:i:s');
    }
  }
  return date('Y-m-d H:i:s');
}

function prepareLocationRow(array $row)
{
  if (isset($row['latitude'], $row['longitude'])) {
    $lat = $row['latitude'];
    $lon = $row['longitude'];
    if ($lat !== null && $lon !== null) {
      $row['geo'] = "ST_GeomFromText('POINT($lat $lon)', 4326)";
    }
  }
  unset($row['latitude'], $row['longitude']);
  return $row;
}

function buildInsertStatement($db_table, array $row)
{
  $keys = array_keys($row);
  $fields = '`' . implode('`,`', $keys) . '`';
  $placeholders = [];
  $values = [];
  foreach ($keys as $k) {
    if ($k === 'geo' && strpos($row[$k], 'ST_GeomFromText') === 0) {
      $placeholders[] = $row[$k];
    } else {
      $placeholders[] = '?';
      $values[] = $row[$k];
    }
  }
  $values_clause = implode(',', $placeholders);
  $sql = "INSERT IGNORE INTO `$db_table` ($fields) VALUES ($values_clause)";
  return [$sql, $values];
}

function insertRow($mysqli, $sql, $values)
{
  $stmt = $mysqli->prepare($sql);
  if (!$stmt) {
    logMsg(PREPARE_FAILED_MSG . $mysqli->error, true);
    return false;
  }
  if ($values) {
    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);
  }
  if (!$stmt->execute()) {
    logMsg("Execute failed: " . $stmt->error, true);
    $stmt->close();
    return false;
  }
  $stmt->close();
  return true;
}

function importTable($mysqli, $table, $db_table, $rows)
{
  logMsg("Importing $table -> $db_table (" . count($rows) . " rows)");
  $mysqli->begin_transaction();
  foreach ($rows as $row) {
    if ($table === 'locations') {
      $row = prepareLocationRow($row);
    }
    list($sql, $values) = buildInsertStatement($db_table, $row);
    if (!insertRow($mysqli, $sql, $values)) {
      logMsg("Error preparing SQL for $db_table: " . $mysqli->error . " Row: " . json_encode($row), true);
    }
  }
  $mysqli->commit();
}

function importData($mysqli, $data)
{
  $table_order = [
    'persons',
    'systems',
    'genres',
    'conventionsets',
    'tags',
    'titles',
    'presentations',
    'feeds',
    'magazines',
    'awards',
    'locations',
    'sitetexts',
    'games',
    'conventions',
    'issues',
    'gametags',
    'gameruns',
    'gamedescriptions',
    'trivia',
    'links',
    'aliases',
    'award_categories',
    'award_nominees',
    'award_nominee_entities',
    'person_game_title_relations',
    'game_convention_presentation_relations',
    'person_convention_relations',
    'articles',
    'contributors',
    'article_reference',
    'location_reference',
    'files'
  ];
  $table_map = [
    'persons' => 'person',
    'systems' => 'gamesystem',
    'genres' => 'genre',
    'conventionsets' => 'conset',
    'conventions' => 'convention',
    'games' => 'game',
    'titles' => 'title',
    'presentations' => 'presentation',
    'magazines' => 'magazine',
    'issues' => 'issue',
    'articles' => 'article',
    'award_categories' => 'award_categories',
    'award_nominees' => 'award_nominees',
    'award_nominee_entities' => 'award_nominee_entities',
    'files' => 'files',
    'gamedescriptions' => 'game_description',
    'gameruns' => 'gamerun',
    'tags' => 'tag',
    'gametags' => 'tags',
    'trivia' => 'trivia',
    'links' => 'links',
    'contributors' => 'contributor',
    'person_game_title_relations' => 'pgrel',
    'game_convention_presentation_relations' => 'cgrel',
    'person_convention_relations' => 'pcrel',
    'article_reference' => 'article_reference',
    'location_reference' => 'lrel',
    'locations' => 'locations',
    'aliases' => 'alias',
    'sitetexts' => 'weblanguages',
    'awards' => 'awards',
    'genre_game_relations' => 'ggrel'
  ];
  foreach ($table_order as $table) {
    if (!empty($data[$table])) {
      $db_table = $table_map[$table] ?? $table;
      importTable($mysqli, $table, $db_table, $data[$table]);
    }
  }
  // Set installation status to 'live'
  if (!$mysqli->query("INSERT INTO `installation` (`key`, `value`) VALUES ('status', 'live') ON DUPLICATE KEY UPDATE `value` = 'live'")) {
    logMsg("Error setting installation status: " . $mysqli->error, true);
  }
  logMsg("Import complete.");
}

function importNews($mysqli, $rss_content)
{
  if (!$rss_content) {
    return;
  }
  $xml = @simplexml_load_string($rss_content);
  if (!$xml || !isset($xml->channel->item)) {
    logMsg("Invalid RSS XML. Skipping news import.", true);
    return;
  }
  $imported = 0;
  $skipped = 0;
  foreach ($xml->channel->item as $item) {
    $desc = html_entity_decode((string)$item->description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $pubdate = parseRssDate((string)$item->pubDate);
    // Check for duplicates
    $stmt = $mysqli->prepare("SELECT id FROM news WHERE text = ? AND published = ?");
    if (!$stmt) {
      logMsg(PREPARE_FAILED_MSG . $mysqli->error, true);
      continue;
    }
    $stmt->bind_param('ss', $desc, $pubdate);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $skipped++;
      $stmt->close();
      continue;
    }
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO news (text, published, online) VALUES (?, ?, 1)");
    if (!$stmt) {
      logMsg(PREPARE_FAILED_MSG . $mysqli->error, true);
      continue;
    }
    $stmt->bind_param('ss', $desc, $pubdate);
    if ($stmt->execute()) {
      $imported++;
      logMsg("Imported: " . substr($desc, 0, 50) . "... ($pubdate)");
    } else {
      logMsg("Insert failed: " . $stmt->error, true);
    }
    $stmt->close();
  }
  logMsg("Import complete: $imported imported, $skipped skipped");
}

// --- Main Execution ---
$mysqli = waitForMysql(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Main DB import if empty
$res = $mysqli->query("SELECT COUNT(*) FROM person");
if (!$res) {
  exitWithError("Failed to query person table: " . $mysqli->error);
}
$row = $res->fetch_row();
if ($row[0] == 0) {
  $data = fetchJson(ALEXANDRIA_URL);
  importData($mysqli, $data);
} else {
  logMsg("Database already populated ($row[0] rows in person table). Skipping main import.");
}

// News import if empty
$res = $mysqli->query("SELECT COUNT(*) FROM news");
if (!$res) {
  exitWithError("Failed to query news table: " . $mysqli->error);
}
$row = $res->fetch_row();
if ($row[0] == 0) {
  $rss_content = fetchRss(RSS_URL);
  importNews($mysqli, $rss_content);
} else {
  logMsg("News table already populated ($row[0] rows). Skipping news import.");
}

$mysqli->close();
