<?php
// Download and insert files from Golden Cobra 2020-2023 to Alexandria
// Based on Evan Torner's permission
define("DOWNLOAD_PATH", "../../loot.alexandria.dk/files/");
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";
chdir("adm/");
$action = $_REQUEST['action'] ?? '';

if (!function_exists('mb_basename')) {
  function mb_basename($path)
  {
    return array_reverse(explode("/", $path))[0];
  }
}

function create_file($data_id, $category, $path, $description, $downloadable, $language)
{
  $allowed_extensions = ["pdf", "txt", "doc", "docx", "zip", "rar", "mp3", "pps", "jpg", "png", "gif", "webp"];
  $path = trim($path);
  $filename = mb_basename($path);
  $description = trim($description);
  $downloadable = ($downloadable ? 1 : 0);
  $extension = strtolower(substr(strrchr($path, "."), 1));
  $data_field = getFieldFromCategory($category);
  if (!file_exists($path)) {
    $_SESSION['admin']['info'] = "Error: The files does not exist: $path";
  } elseif (!in_array($extension, $allowed_extensions)) {
    $_SESSION['admin']['info'] = "Error: Not a vaild file type: $path";
  } elseif (!$data_field) {
    $_SESSION['admin']['info'] = "Error: Unknown category";
  } else {
    doquery("INSERT INTO files (`$data_field`, filename, description, downloadable, language, inserted) VALUES ('$data_id','" . dbesc($filename) . "','" . dbesc($description) . "','$downloadable','" . dbesc($language) . "', NOW() )");
    $_SESSION['admin']['info'] = "The file has been created." . dberror();
    chlog($data_id, $category, "File created: " . $filename);
  }
}

function upload_remote_file($remoteurl, $category, $data_id)
{
  $allowed_extensions = ["pdf", "txt", "doc", "docx", "zip", "rar", "mp3", "pps", "jpg", "png", "gif", "webp"];
  $allowed_schemes = ['http', 'https', 'ftp', 'ftps'];
  $urldata = parse_url($remoteurl);
  $basename = urldecode(mb_basename($urldata['path']));
  if (!$basename) {
    $basename = "scenarie_" . $data_id . ".pdf";
  }
  $upload_path = DOWNLOAD_PATH . getcategorydir($category) . "/" . $data_id . "/" . $basename;
  $upload_dir = dirname($upload_path);
  $pathinfo = pathinfo($upload_path);
  if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775);
  }
  if (!in_array($urldata['scheme'], $allowed_schemes)) {
    $_SESSION['admin']['info'] = "Error: Not a valid URL";
  } elseif (!in_array(strtolower($pathinfo['extension']), $allowed_extensions)) {
    $_SESSION['admin']['info'] = "Error: Not a valid file type (" . (!is_null($pathinfo['extension']) ? "." . htmlspecialchars($pathinfo['extension']) : "blank") . ")";
  } elseif (file_exists($upload_path)) {
    $_SESSION['admin']['info'] = "Error: A file with this file name already exists";
  } elseif (!copy($remoteurl, $upload_path)) {
    $_SESSION['admin']['info'] = "Unknown error when transferring file: " . error_get_last()['message'];
  } else {
    chmod($upload_path, 0664);
    $_SESSION['admin']['info'] = "The file has been uploaded.";
    chlog($data_id, $category, "File remote uploaded: " . $remoteurl);
    $remoteurl = "";
  }
  return $upload_path;
}

htmladmstart("Import Golden Cobra Files");
ini_set('display_errors', 1);
error_reporting(E_ALL);

$games = getall("
    SELECT g.id, g.title, l.url, l.description
    FROM game g
    INNER JOIN cgrel cg ON g.id = cg.game_id
    INNER JOIN convention c ON cg.convention_id = c.id AND c.year >= 2020 AND c.conset_id = 183 -- Golden Cobra
    INNER JOIN links l ON g.id = l.game_id AND l.description = '{\$_sce_file_scenario}'
    WHERE g.id NOT IN (SELECT COALESCE(game_id,0) FROM files f)
    LIMIT 20
");

print '<h1>Import Golden Cobra Files:</h1>' . PHP_EOL;
print '<form method="post"><input type="hidden" name="action" value="import"><input type="submit" value="Import"></form>';
print '<h2>Candidates:</h2>' . PHP_EOL;
foreach ($games as $game) {
  $game_id = $game['id'];
  print '<hr><p>';
  print 'Game ID: <a href="game.php?game=' . $game_id . '">' . $game_id . '</a><br>';
  print 'Title: ' . htmlspecialchars($game['title']) . '<br>';
  print 'URL: ' . htmlspecialchars($game['url']);
  print '</p>';
  if ($action == 'import') {
    $description = '{$_sce_file_scenario}';
    $upload_path = upload_remote_file($game['url'], 'game', $game['id']);
    print "<p><b>Downloaded: " . $_SESSION['admin']['info'] . "</b></p>";
    create_file($game_id, 'game', $upload_path, $description, 1, 'en');
    print "<p><b>Added: " . $_SESSION['admin']['info'] . "</b></p>";
  }
}
