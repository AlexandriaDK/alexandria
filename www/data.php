<?php
require_once "./connect.php";
require_once "base.inc.php";

$person = (int) ($_REQUEST['person'] ?? 0);
$scenarie = (int) ($_REQUEST['scenarie'] ?? 0);
$game = (int) ($_REQUEST['game'] ?? 0);
$con = (int) ($_REQUEST['con'] ?? 0);
$conset = (int) ($_REQUEST['conset'] ?? 0);
$system = (int) ($_REQUEST['system'] ?? 0);
$year = (int) ($_REQUEST['year'] ?? 0);
$tag = (string) ($_REQUEST['tag'] ?? '');
$review = (int) ($_REQUEST['review'] ?? 0);

if ($person) {
  include_once "person.inc.php";
} elseif ($scenarie || $game) {
  include_once "game.inc.php";
} elseif ($con) {
  include_once "convention.inc.php";
} elseif ($conset) {
  include_once "conset.inc.php";
} elseif ($system) {
  include_once "gamesystem.inc.php";
} elseif ($year) {
  include_once "year.inc.php";
} elseif ($tag) {
  include_once "tag.inc.php";
} elseif ($review) {
  include_once "review.inc.php";
} else {
  include_once "default.inc.php";
}
