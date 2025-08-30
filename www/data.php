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
  include("person.inc.php");
} elseif ($scenarie || $game) {
  include("game.inc.php");
} elseif ($con) {
  include("convention.inc.php");
} elseif ($conset) {
  include("conset.inc.php");
} elseif ($system) {
  include("gamesystem.inc.php");
} elseif ($year) {
  include("year.inc.php");
} elseif ($tag) {
  include("tag.inc.php");
} elseif ($review) {
  include("review.inc.php");
} else {
  include("default.inc.php");
}
