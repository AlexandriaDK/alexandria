<?php
require("./connect.php");
require("base.inc.php");

// redirect if no user
if (!isset($_SESSION['user_id']) && !$user_id) {
  header("Location: ./");
  exit;
}

$achievement = $_GET['achievement'] ?? "";
$o = $_GET['o'] ?? "";

// achievements
if ($achievement == 'clickme') {
  award_achievement(77);
  header("Location: myhistory");
  exit;
}

if ($achievement == 'createaguiinterfaceusingvisualbasic') {
  award_achievement(81);
  header("Location: myhistory");
  exit;
}

if ($achievement == 'upupdowndownleftrightleftrightba') {
  award_achievement(84);
  header("Location: myhistory");
  exit;
}




function getmyachievements($user_id)
{
  $user_id = (int) $user_id;
  $query = "
		SELECT achievements.id, label, description, user_achievements.completed
		FROM achievements
		LEFT JOIN user_achievements ON achievements.id = user_achievements.achievement_id AND user_achievements.user_id = $user_id
		WHERE achievements.available = 1
		ORDER BY achievements.id
	";
  $data = getall($query);
  return $data;
}

function getmysce($user_id, $o = 0)
{
  switch ($o) {
    case 1:
      $order = "title_translation, `read` desc, gmed desc, played desc";
      break;
    case 2:
      $order = "`read` desc, title_translation, gmed desc, played desc";
      break;
    case 3:
      $order = "gmed desc, title_translation, `read` desc, played desc";
      break;
    case 4:
      $order = "played desc, title_translation, `read` desc, gmed desc";
      break;
    default:
      $order = "title_translation, `read` desc, gmed desc, played desc";
      break;
  }
  $q = "SELECT g.id, g.title, g.boardgame, SUM(userlog.type = 'read') AS `read`, SUM(userlog.type = 'gmed') AS gmed, SUM(userlog.type = 'played') AS played, COALESCE(alias.label, g.title) AS title_translation
	     FROM userlog
		 INNER JOIN game g ON userlog.game_id = g.id
		 LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	     WHERE userlog.user_id = '$user_id' AND userlog.game_id IS NOT NULL
	     GROUP BY userlog.game_id
	     ORDER BY $order";
  $data = getall($q);
  return $data;
}

function getmyconvention($user_id, $o = 0)
{
  switch ($o) {
    case 5:
      $order = "convention.name, convention.year, convention.begin, convention.end";
      break;
    case 6:
      $order = "convention.year, convention.begin, convention.end, convention.name";
      break;
    case 7:
      $order = "conset.name, convention.year, convention.begin, convention.end, convention.name";
      break;
    default:
      $order = "convention.year, convention.begin, convention.end, convention.name";
      break;
  }
  $q = "SELECT convention.id, convention.name, convention.year, conset.name AS conset_name FROM userlog, convention LEFT JOIN conset ON convention.conset_id = conset.id WHERE userlog.user_id = '$user_id' AND userlog.convention_id = convention.id ORDER BY $order";
  $data = getall($q);
  return $data;
}

// get user_id from logged-in session data
$user_id = (int) $_SESSION['user_id'];

$content_addentry = "";
$content_myconvents = "";
$content_myscenarios = "";
$content_personal_achievements = "";

$conventions = getmyconvention($user_id, $o);
if ($conventions) {
  #	$content_myconvents .= "<h3 class=\"parttitle\">Kongresser: (".count($conventions).")</h3>\n";

  #	$content_myconvents .= "<table><tr><td></td><td><a href=\"myhistory?o=5\">Navn</a></td><td><a href=\"myhistory?o=7\">Serie</a></td><td><a href=\"myhistory?o=6\">År</a></td></tr>";
  $str_visited = $t->getTemplateVars('_top_visited_pt');
  foreach ($conventions as $convention) {
    $spanid = "convent_" . $convention['id'] . "_visited";
    $content_myconvents .= "<tr>";
    $content_myconvents .= "<td>" . getdynamicconventionhtml($convention['id'], 'visited', TRUE) . "</td>";
    $content_myconvents .= "<td>" . getdatahtml('convention', $convention['id'], $convention['name']) . "</td>";
    $content_myconvents .= "<td style=\"text-align: left\">" . $convention['conset_name'] . "</td>";
    $content_myconvents .= "<td style=\"text-align: right\">" . yearname($convention['year']) . "</td>";
    $content_myconvents .= "</tr>\n";
  }
  $content_myconvents .= "</table>\n";
}

$games = getmysce($user_id, $o);
if ($games) {

  foreach ($games as $game) {
    $content_myscenarios .= "<tr>";
    $content_myscenarios .= "<td><span title=\"" . htmlspecialchars($game['title']) . "\">" . getdatahtml('game', $game['id'], $game['title_translation']) . "</span></td>";
    if ($game['boardgame']) {
      $options = getuserlogoptions('boardgame');
    } else {
      $options = getuserlogoptions('scenario');
    }
    foreach ($options as $type) {
      if ($type) {
        $content_myscenarios .= "<td style=\"text-align: center\">" . getdynamicgamehtml($game['id'], $type, $game[$type]) . "</td>";
      } else {
        $content_myscenarios .= "<td></td>";
      }
    }
    $content_myscenarios .= "</tr>\n";
  }

  // check for achievements
  $read = $gmed = $played = $visited = 0;
  foreach ($games as $game) {
    if ($game['read'] == 1) $read++;
    if ($game['gmed'] == 1) $gmed++;
    if ($game['played'] == 1) $played++;
  }
  $visited = count($conventions);
  if ($read >= 100)   award_achievement(6);  // read +100 scenarios
  if ($gmed >= 50)    award_achievement(7);  // gmed +50 scenarios
  if ($played >= 100) award_achievement(70);  // played +100 scenarios
  if ($visited >= 50) award_achievement(69); // visited +50 conventions

}

$achievements = getmyachievements($user_id);
$achievement_count = 0;
foreach ($achievements as $achievement) {
  if ($achievement['completed']) {
    $achievement_count++;
    $content_personal_achievements .= '<div class="achievement completed" title="' . pubdateprint($achievement['completed']) . '">';
  } else {
    if ($achievement['id'] == 77) { // click me!
      $content_personal_achievements .= '<div class="achievement incomplete" onclick="location.href=\'/myhistory?achievement=clickme\';">';
    } else {
      $content_personal_achievements .= '<div class="achievement incomplete">';
    }
  }
  $content_personal_achievements .= '<span class="label" id="achievement_id_' . (int) $achievement['id'] . '">' . htmlspecialchars($achievement['label']) . '</span>';
  if ($achievement['completed']) {
    $content_personal_achievements .= '<br />' . htmlspecialchars($achievement['description']);
  }
  $content_personal_achievements .= '</div>';
  $content_personal_achievements .= "\n";
}
#$content_personal_achievements = '<h3>Achievements: (' . $achievement_count . ')</h3>' . $content_personal_achievements;


$t->assign('content_addentry', $content_addentry);
$t->assign('content_myconvents', $content_myconvents);
$t->assign('content_myscenarios', $content_myscenarios);
$t->assign('content_personal_achievements', $content_personal_achievements);
$t->assign('con_count', count($conventions));
$t->assign('game_count', count($games));
$t->assign('game_read', $read);
$t->assign('game_gmed', $gmed);
$t->assign('game_played', $played);
$t->assign('achievement_count', $achievement_count);
$t->display('myhistory.tpl');
