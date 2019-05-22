<?php
require("./connect.php");
require("base.inc");
require("template.inc");

// redirect if no user
if (!$_SESSION['user_id']) {
	header("Location: /");
	exit;
}

// achievements
if ($_REQUEST['achievement'] == 'clickme') {
	award_achievement(77);
	header("Location: /myhistory");
	exit;
}

if ($_REQUEST['achievement'] == 'createaguiinterfaceusingvisualbasic') {
	award_achievement(81);
	header("Location: /myhistory");
	exit;
}

if ($_REQUEST['achievement'] == 'upupdowndownleftrightleftrightba') {
	award_achievement(84);
	header("Location: /myhistory");
	exit;
}


function getmyachievements ($user_id) {
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

function getmysce ($user_id, $o = 0) {
	switch ($o) {
		case 1: $order = "title, `read` desc, gmed desc, played desc"; break;
		case 2: $order = "`read` desc, title, gmed desc, played desc"; break;
		case 3: $order = "gmed desc, title, `read` desc, played desc"; break;
		case 4: $order = "played desc, title, `read` desc, gmed desc"; break;
		default: $order = "title, `read` desc, gmed desc, played desc"; break;
	}
	$q = "SELECT sce.id, sce.title, sce.boardgame, SUM(userlog.type = 'read') AS `read`, SUM(userlog.type = 'gmed') AS gmed, SUM(userlog.type = 'played') AS played ".
	     "FROM userlog, sce ".
	     "WHERE userlog.user_id = '$user_id' AND userlog.category = 'sce' AND userlog.data_id = sce.id ".
	     "GROUP BY userlog.data_id ".
	     "ORDER BY $order";
	$data = getall($q);
	return $data;
}

function getmyconvent ($user_id, $o = 0) {
	switch ($o) {
		case 5: $order = "convent.name, convent.year, convent.begin, convent.end"; break;
		case 6: $order = "convent.year, convent.begin, convent.end, convent.name"; break;
		case 7: $order = "conset.name, convent.year, convent.begin, convent.end, convent.name"; break;
		default: $order = "convent.year, convent.begin, convent.end, convent.name"; break;
	}
	$q = "SELECT convent.id, convent.name, convent.year, conset.name AS conset_name FROM userlog, convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE userlog.user_id = '$user_id' AND userlog.category = 'convent' AND userlog.data_id = convent.id ORDER BY $order";
	$data = getall($q);
	return $data;
}

// get user_id from logged-in session data
$user_id = (int) $_SESSION['user_id'];

$content_addentry = "";
$content_myconvents = "";
$content_myscenarios = "";
$content_personal_achievements = "";

$conventions = getmyconvent($user_id,$_REQUEST['o']);
if ($conventions) {
#	$content_myconvents .= "<h3 class=\"parttitle\">Kongresser: (".count($conventions).")</h3>\n";

#	$content_myconvents .= "<table><tr><td></td><td><a href=\"myhistory?o=5\">Navn</a></td><td><a href=\"myhistory?o=7\">Serie</a></td><td><a href=\"myhistory?o=6\">År</a></td></tr>";
	foreach ($conventions AS $convent) {
		$spanid = "convent_".$convent['id']."_visited";
		$content_myconvents .= "<tr>";
		$content_myconvents .= "<td>".
			                     "<span id=\"".$spanid."\">".
			                     "<a href=\"javascript:switchicon('".$spanid."','remove','convent',".$convent['id'].",'visited')\">".
			                     "<img src=\"gfx/visited_active.jpg\" alt=\"Besøgt active\" title=\"Besøgt\" border=\"0\" />".
			                     "</a>".
			                     "</span>".
			                     "</td>";

		$content_myconvents .= "<td>".getdataurl('convent',$convent['id'],$convent['name'])."</td>";
		$content_myconvents .= "<td style=\"text-align: left\">".$convent['conset_name']."</td>";
		$content_myconvents .= "<td style=\"text-align: right\">".$convent['year']."</td>";
		$content_myconvents .= "</tr>\n";
	}
	$content_myconvents .= "</table>\n";

}


$scenarios = getmysce($user_id,$_REQUEST['o']);
if ($scenarios) {
	$scenariotext = array("read" => "Læst", "gmed" => "Kørt", "played" => "Spillet");

	foreach ($scenarios AS $scenario) {
		$content_myscenarios .= "<tr>";
		$content_myscenarios .= "<td>".getdataurl('sce',$scenario['id'],$scenario['title'])."</td>";
		if ($scenario['boardgame']) {
			$options = getuserlogoptions('boardgame');
		} else {
			$options = getuserlogoptions('scenario');
		}
		foreach($options AS $type) {
			$spanid = "sce_".$scenario['id']."_".$type;
#			$content_myscenarios .= "<td style=\"text-align: center\"><span id=\"".$spanid."\"><a href=\"javascript:switchicon('".$spanid."','".($scenario[$type]?'remove':'add')."','sce',".$scenario['id'].",'".$type."')\">".(0+$scenario[$type])."</a></td>";
			if ($type) {
				$content_myscenarios .= "<td style=\"text-align: center\">".
							"<span id=\"".$spanid."\">".
							"<a href=\"javascript:switchicon('".$spanid."','".($scenario[$type]?'remove':'add')."','sce',".$scenario['id'].",'".$type."')\">".
							"<img src=\"gfx/".$type."_".($scenario[$type]?'active':'passive').".jpg\" alt=\"".$scenariotext[$type]." ".($scenario[$type]?'active':'passive')."\" title=\"".$scenariotext[$type]."\" border=\"0\" />".
							"</a>".
							"</span>".
							"</td>";
			} else {
				$content_myscenarios .= "<td></td>";
			}
		}
		$content_myscenarios .= "</tr>\n";
	}

	// check for achievements
	$read = $gmed = $played = $visited = 0;
	foreach ($scenarios AS $scenario) {
		if ($scenario['read'] == 1) $read++;
		if ($scenario['gmed'] == 1) $gmed++;
		if ($scenario['played'] == 1) $played++;
	}
	$visited = count($conventions);
	if ($read >= 100)   award_achievement(6);  // read +100 scenarios
	if ($gmed >= 50)    award_achievement(7);  // gmed +50 scenarios
	if ($played >= 100) award_achievement(70);  // played +100 scenarios
	if ($visited >= 50) award_achievement(69); // visited +50 conventions

	// assemble content
/*
	$content_myscenarios = "<div style=\"float: left;\" >" . 
	                       "<h3 class=\"parttitle\">Scenarier: (".count($scenarios) . ": $read/$gmed/$played)</h3>" .
	                       "<table><tr><td><a href=\"myhistory?o=1\">Titel</a></td><td><a href=\"myhistory?o=2\">Læst</a></td><td><a href=\"myhistory?o=3\">Kørt</a></td><td><a href=\"myhistory?o=4\">Spillet</a></td></tr>" . 
	                       $content_myscenarios .
	                       "</table>\n" . 
	                       "</div>\n";
*/
}

$achievements = getmyachievements($user_id);
$achievement_count = 0;
foreach ($achievements AS $achievement) {
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


$t->assign('content_addentry',$content_addentry);
$t->assign('content_myconvents',$content_myconvents);
$t->assign('content_myscenarios',$content_myscenarios);
$t->assign('content_personal_achievements',$content_personal_achievements);
$t->assign('con_count',count($conventions) );
$t->assign('game_count',count($scenarios) );
$t->assign('game_read',$read);
$t->assign('game_gmed',$gmed);
$t->assign('game_played',$played);
$t->assign('achievement_count', $achievement_count );
$t->display('myhistory.tpl');

?>
