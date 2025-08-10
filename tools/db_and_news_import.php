<?php
// Alexandria DB and news import script (PHP CLI)
// Usage: php db_and_news_import.php

ini_set('memory_limit', '1G');
set_time_limit(0);

$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_USER = getenv('DB_USER') ?: 'alexuser';
$DB_PASS = getenv('DB_PASS') ?: 'alexpass';
$DB_NAME = getenv('DB_NAME') ?: 'alexandria';
$ALEXANDRIA_URL = getenv('ALEXANDRIA_URL') ?: 'https://alexandria.dk/en/export?dataset=all';
$RSS_URL = getenv('RSS_URL') ?: 'https://alexandria.dk/rss.php';

function wait_for_mysql($mysqli)
{
  for ($i = 0; $i < 60; $i++) {
    if ($mysqli->connect_errno === 0) return;
    echo "Waiting for MySQL...\n";
    sleep(2);
  }
  echo "MySQL not ready after 2 minutes, exiting.\n";
  exit(1);
}

function fetch_json($url)
{
  echo "Downloading data from $url\n";
  $json = file_get_contents($url);
  if ($json === false) {
    echo "Error: Failed to download JSON from $url\n";
    exit(1);
  }
  $data = json_decode($json, true);
  if (!$data || !isset($data['result'])) {
    echo "Error: Invalid JSON\n";
    exit(1);
  }
  return $data['result'];
}

function fetch_rss($url)
{
  echo "Downloading RSS from $url\n";
  $rss = file_get_contents($url);
  if ($rss === false) {
    echo "Warning: Failed to download RSS from $url. Skipping news import.\n";
    return null;
  }
  return $rss;
}

function parse_rss_date($date_str)
{
  $parts = explode(' ', $date_str);
  if (count($parts) >= 5) {
    $date_part = implode(' ', array_slice($parts, 1, 4));
    $dt = DateTime::createFromFormat('d M Y H:i:s', $date_part);
    if ($dt) return $dt->format('Y-m-d H:i:s');
  }
  return date('Y-m-d H:i:s');
}

function import_data($mysqli, $data)
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
    if (empty($data[$table])) continue;
    $db_table = $table_map[$table] ?? $table;
    echo "Importing $table -> $db_table (" . count($data[$table]) . " rows)\n";
    $mysqli->begin_transaction();
    foreach ($data[$table] as $row) {
      // Geometry for locations
      if ($table === 'locations') {
        if (isset($row['latitude'], $row['longitude'])) {
          $exported_lat = $row['latitude'];
          $exported_lon = $row['longitude'];
          if ($exported_lat !== null && $exported_lon !== null) {
            $row['geo'] = "ST_GeomFromText('POINT($exported_lat $exported_lon)', 4326)";
          }
        }
        unset($row['latitude'], $row['longitude']); // Always remove, even if not set
      }
      $keys = array_keys($row);
      $fields = '`' . implode('`,`', $keys) . '`';
      $placeholders = [];
      $values = [];
      foreach ($keys as $k) {
        if ($k === 'geo' && strpos($row[$k], 'ST_GeomFromText') === 0) {
          $placeholders[] = $row[$k]; // raw SQL
        } else {
          $placeholders[] = '?';
          $values[] = $row[$k];
        }
      }
      $values_clause = implode(',', $placeholders);
      $sql = "INSERT IGNORE INTO `$db_table` ($fields) VALUES ($values_clause)";
      $stmt = $mysqli->prepare($sql);
      if ($stmt) {
        if ($values) {
          $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        }
        $stmt->execute();
        $stmt->close();
      } else {
        echo "Error preparing SQL for $db_table: " . $mysqli->error . "\nRow: " . json_encode($row) . "\n";
      }
    }
    $mysqli->commit();
  }
  // Set installation status to 'live'
  if (!$mysqli->query("INSERT INTO `installation` (`key`, `value`) VALUES ('status', 'live') ON DUPLICATE KEY UPDATE `value` = 'live'")) {
    echo "Error setting installation status: " . $mysqli->error . "\n";
  }
  echo "Import complete.\n";
}

function import_news($mysqli, $rss_content)
{
  if (!$rss_content) return;
  $xml = new SimpleXMLElement($rss_content);
  $imported = 0;
  $skipped = 0;
  foreach ($xml->channel->item as $item) {
    $desc = html_entity_decode((string)$item->description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $pubdate = parse_rss_date((string)$item->pubDate);
    // Check for duplicates
    $stmt = $mysqli->prepare("SELECT id FROM news WHERE text = ? AND published = ?");
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
    $stmt->bind_param('ss', $desc, $pubdate);
    $stmt->execute();
    $stmt->close();
    $imported++;
    echo "Imported: " . substr($desc, 0, 50) . "... ($pubdate)\n";
  }
  echo "\nImport complete: $imported imported, $skipped skipped\n";
}

// Main
$maxTries = 60;
$tries = 0;
do {
  $mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
  if ($mysqli->connect_errno === 0) {
    break;
  }
  echo "Waiting for MySQL...\n";
  sleep(2);
  $tries++;
} while ($tries < $maxTries);
if ($mysqli->connect_errno !== 0) {
  echo "MySQL not ready after $maxTries tries, exiting.\n";
  exit(1);
}

// Main DB import if empty
$res = $mysqli->query("SELECT COUNT(*) FROM person");
$row = $res->fetch_row();
if ($row[0] == 0) {
  $data = fetch_json($ALEXANDRIA_URL);
  import_data($mysqli, $data);
} else {
  echo "Database already populated ($row[0] rows in person table). Skipping main import.\n";
}

// News import if empty
$res = $mysqli->query("SELECT COUNT(*) FROM news");
$row = $res->fetch_row();
if ($row[0] == 0) {
  $rss_content = fetch_rss($RSS_URL);
  import_news($mysqli, $rss_content);
} else {
  echo "News table already populated ($row[0] rows). Skipping news import.\n";
}

$mysqli->close();
