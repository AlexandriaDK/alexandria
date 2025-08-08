<?php
require("./connect.php");
require("base.inc.php");

if (isset($_SESSION) && isset($_SESSION['user_id'])) {
  $userlog = getuserloggames($_SESSION['user_id']);
}

$titlepart = "";
$beginchar = "";

// hent bogstaver
$chars = range('a', 'z');
$chars[] = "æ";
$chars[] = "ø";
$chars[] = "å";
$chars[] = "1";

$keys = "";
foreach ($chars as $char) {
  $keys .= "\n\t\t<a href=\"scenarier?b=" . rawurlencode($char) . "\">" . mb_strtoupper($char == "1" ? "0-9#" : $char) . "</a>";
}

// fetch genres
$genre = [];
$genres = getcolid("SELECT g.id, g.name FROM genre g ORDER BY g.name");
foreach ($genres as $gid => $gname) {
  $genre[] = '<a href="scenarier?g=' . $gid . '">' . htmlspecialchars($gname) . '</a>';
}
$genre = join(", ", $genre);

$b = (string) ($_REQUEST["b"] ?? "");
$g = (int) ($_REQUEST["g"] ?? "");

if ($b == "") {
  $b = "a";
}

if ($g) {
  $wherepart = "LEFT JOIN ggrel ON g.id = ggrel.game_id WHERE ggrel.genre_id = $g";
} else {
  if ($b == "1") {
    $beginchar = "1";
    $wherepart = "COALESCE(alias.label, g.title) REGEXP '^[^a-zæøå]'";
  } elseif (in_array($b, $chars)) {
    $beginchar = $b;
    $wherepart = "COALESCE(alias.label, g.title) LIKE '$b%'";
  } else {
    $beginchar = "a";
    $wherepart = "COALESCE(alias.label, g.title) LIKE 'a%'";
  }
  if ($wherepart) {
    $wherepart = "WHERE " . $wherepart;
  }
}
if ($wherepart) {
  $wherepart .= " AND g.boardgame = 0";
} else {
  $wherepart = "WHERE g.boardgame = 0";
}

// Find all games, including persons and cons - restrict to one premiere convention
$r = getall("
	SELECT p.id AS personid, CONCAT(p.firstname,' ',p.surname) AS personname, g.id, g.title, g.boardgame, c.id AS convention_id, c.name AS convent_name, c.year, c.begin, c.end, c.cancelled, COUNT(f.id) AS files, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	LEFT JOIN cgrel ON g.id = cgrel.game_id AND cgrel.presentation_id = 1
	LEFT JOIN convention c ON cgrel.convention_id = c.id
	LEFT JOIN pgrel ON g.id = pgrel.game_id AND pgrel.title_id = 1
	LEFT JOIN person p ON pgrel.person_id = p.id
	LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	$wherepart
	GROUP BY cgrel.presentation_id,cgrel.game_id,pgrel.person_id, g.id, c.id, alias.label
	ORDER BY title_translation, p.surname, p.firstname, c.year, c.begin, c.end
");

$xscenlist = "";

// Create preliminary scenario list 
$scenarios = [];
foreach ($r as $row) {
  $game_id = $row['id'];
  $scenarios[$game_id]['title'] = $row['title_translation'];
  $scenarios[$game_id]['origtitle'] = $row['title'];
  $scenarios[$game_id]['boardgame'] = $row['boardgame'];
  $scenarios[$game_id]['person'][$row['personid']] = ['name' => $row['personname']];
  $scenarios[$game_id]['con'][$row['convention_id']] = ['id' => $row['convention_id'], 'name' => $row['convent_name'], 'year' => $row['year'], 'cancelled' => $row['cancelled'], 'begin' => $row['begin'], 'end' => $row['end']];
  $scenarios[$game_id]['downloadable'] = ($row['files'] > 0);
}

foreach ($scenarios as $scenario_id => $scenario) {
  $xscenlist .= "\t<tr>\n";
  if (isset($_SESSION) && isset($_SESSION['user_id'])) {
    if ($row['boardgame']) {
      $options = getuserlogoptions('boardgame');
    } else {
      $options = getuserlogoptions('scenario');
    }

    foreach ($options as $type) {
      $xscenlist .= "<td>";
      if ($type != NULL) {
        $xscenlist .= getdynamicgamehtml($scenario_id, $type, $userlog[$scenario_id][$type] ?? FALSE);
      }
      $xscenlist .= "</td>";
    }
    $xscenlist .= "<td style=\"width: 10px;\">&nbsp;</td>";
  }
  if ($scenario['downloadable']) {
    $xscenlist .= "<td><a href=\"data?scenarie=" . $scenario_id . "\" title=\"" . htmlspecialchars($t->getTemplateVars('_sce_downloadable')) . "\">💾</a></td>";
  } else {
    $xscenlist .= "<td></td>";
  }
  $xscenlist .= "\t\t<td><a href=\"data?scenarie=" . $scenario_id . "\" class=\"game\" title=\"" . htmlspecialchars($scenario['origtitle']) . "\">" . htmlspecialchars($scenario['title']) . "</a></td>\n";

  // persons
  $xscenlist .= "\t\t<td>";
  foreach ($scenario['person'] as $person_id => $person) {
    if ($person_id) {
      $xscenlist .= "<a href=\"data?person=" . $person_id . "\" class=\"person\">" . htmlspecialchars($person['name']) . "</a><br>\n";
    }
  }
  $xscenlist .= "</td>\n";

  // convents
  $xscenlist .= "\t\t<td>";
  foreach ($scenario['con'] as $con_id => $convention) {
    if ($con_id) {
      $xscenlist .= smarty_function_con($convention) . "<br>";
    }
  }
  $xscenlist .= "</td>\n";
  $xscenlist .= "\t</tr>\n";
}

// achievements
if ($b == 'c') award_achievement(73); // Scenario beginning with C
if ($g == 9)   award_achievement(74); // Scenario in Thriller genre

$t->assign('keys', $keys);
$t->assign('genre', $genre);
$t->assign('scenlist', $xscenlist);
$t->assign('beginchar', $beginchar);
$t->display('games.tpl');
