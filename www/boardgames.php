<?php
require("./connect.php");
require("base.inc.php");

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

// Find all games, including persons and cons - restrict to one premiere convention
$r = getall("
	SELECT p.id AS autid, CONCAT(p.firstname,' ',p.surname) AS autname, g.id, g.title, g.boardgame, c.id AS convention_id, c.name AS convention_name, c.year, c.begin, c.end, c.cancelled, COUNT(f.id) AS files, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	LEFT JOIN cgrel ON g.id = cgrel.game_id AND cgrel.presentation_id = 1
	LEFT JOIN convention c ON cgrel.convention_id = c.id
	LEFT JOIN pgrel ON g.id = pgrel.game_id AND pgrel.title_id IN (1,5)
	LEFT JOIN person p ON pgrel.person_id = p.id
	LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE g.boardgame = 1
	GROUP BY cgrel.presentation_id,cgrel.game_id,pgrel.person_id, g.id, c.id
	ORDER BY title_translation, p.surname, p.firstname, c.year, c.begin, c.end
");

$last_game_id = 0;
$scenlist = "";

foreach ($r as $row) {
	$game_id = $row['id'];

	$scenlist .= "\t<tr class=\"listresult\">\n";
	if ($_SESSION['user_id']) {
		if ($game_id != $last_game_id) {
			if ($row['boardgame']) {
				$options = getuserlogoptions('boardgame');
			} else {
				$options = getuserlogoptions('scenario');
			}

			foreach ($options as $type) {
				$scenlist .= "<td>";
				if ($type != NULL) {
					$scenlist .= getdynamicgamehtml($row['id'], $type, $userlog[$row['id']][$type] ?? FALSE);
				}
				$scenlist .= "</td>";
			}
			$scenlist .= "<td style=\"width: 10px;\">&nbsp;</td>";
		} else {
			$scenlist .= "<td colspan=\"4\"></td>";
		}
	}

	if ($game_id != $last_game_id && $row['files'] > 0) {
		$scenlist .= "<td><span title=\"" . htmlspecialchars($t->getTemplateVars('_sce_bgdownloadable')) . "\"><a href=\"data?scenarie=" . $game_id . "\">💾</a></span></td>";
	} else {
		$scenlist .= "<td></td>";
	}
	if ($game_id != $last_game_id) {
		$scenlist .= "\t\t<td><a href=\"data?scenarie=" . $game_id . "\" class=\"game\" title=\"" . htmlspecialchars($row['title']) . "\">" . htmlspecialchars($row['title_translation']) . "</a></td>\n";
	} else {
		$scenlist .= "\t\t<td>&nbsp;</td>\n";
	}

	if ($row['autid']) {
		$scenlist .= "\t\t<td><a href=\"data?person={$row['autid']}\" class=\"person\">{$row['autname']}</a></td>\n";
	} else {
		$scenlist .= "\t\t<td>&nbsp;</td>\n";
	}

	if ($game_id != $last_game_id && $row['convention_id']) {
		$class = "con";
		if ($row['cancelled'] == 1) {
			$class .= " cancelled";
		}
		$scenlist .= "\t\t<td>" . smarty_function_con(['id' => $row['convention_id'], 'name' => $row['convention_name'], 'year' => $row['year'], 'begin' => $row['begin'], 'end' => $row['end'], 'cancelled' => $row['cancelled']]) . "</td>\n";
	} else {
		$scenlist .= "\t\t<td>&nbsp;</td>\n";
	}

	$scenlist .= "\t</tr>\n";
	$last_game_id = $game_id;
}

$t->assign('scenlist', $scenlist);
$t->assign('boardgamesonly', TRUE);
$t->display('games.tpl');
