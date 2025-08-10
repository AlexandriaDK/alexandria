<?php
define('LANGNOREDIRECT', true);
require("./connect.php");
require("base.inc.php");

list($category, $data_id, $filename) = preg_split('_/_', $_SERVER['PATH_INFO'], -1, PREG_SPLIT_NO_EMPTY);
$data_id = intval($data_id);
$fileondisk = ALEXFILES . '/' . $category . '/' . $data_id . '/' . $filename;

if (file_exists($fileondisk)) {
  if ($category == 'scenario') $category = 'game';
  $referer = $_SERVER['HTTP_REFERER'];
  $data_field = getFieldFromCategory($category);
  list($file_id) = getrow("SELECT id FROM files WHERE `$data_field` = $data_id");
  doquery("INSERT INTO filedownloads (files_id, data_id, category, accesstime, referer) VALUES ('$file_id','$data_id','$category',NOW(),'" . dbesc($referer) . "')");

  // achievements
  if ($category == 'game') {
    award_achievement(60); // download a scenario
  }

  if ($category == 'game' && ($_SESSION['user_author_id'] ?? false)) {
    $is_author = getone("SELECT 1 FROM pgrel WHERE game_id = '$data_id' AND title_id IN (1,4) AND person_id = '" . $_SESSION['user_author_id'] . "'");
    if ($is_author) {
      award_achievement(85); // download own scenario
    }
  }

  // redirect
  header("Location: https://download.alexandria.dk/files" . $_SERVER['PATH_INFO']);
} else {
  header("HTTP/1.1 404 Not Found");
  die("The file was not found - please contact <a href=\"mailto:peter@alexandria.dk\">peter@alexandria.dk</a>.");
}
