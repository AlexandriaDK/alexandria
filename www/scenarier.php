<?php
require("./connect.php");
require("base.inc.php");

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

$titlepart = "";
$beginchar = "";

// hent bogstaver
$chars = range('a','z');
$chars[] = "æ";
$chars[] = "ø";
$chars[] = "å";
$chars[] = "1";

$keys = "";
foreach($chars AS $char) {
	$keys .= "\n\t\t<a href=\"scenarier?b=".rawurlencode($char)."\">".mb_strtoupper($char == "1" ? "0-9#" : $char)."</a>";
}

// fetch genres
$genre = [];
$genres = getcolid("SELECT gen.id, gen.name FROM gen ORDER BY gen.name");
foreach($genres AS $gid => $gname) {
	$genre[] = '<a href="scenarier?g='.$gid.'">'.htmlspecialchars($gname).'</a>';
}
$genre = join(", ",$genre);

$b = (string) ($_REQUEST["b"] ?? "");
$g = (int) ($_REQUEST["g"] ?? "");

if ($b == "") {
	$b = "a";
}

if ($g) {
	$wherepart = "LEFT JOIN gsrel ON sce.id = gsrel.sce_id WHERE gsrel.gen_id = $g";
} else {
	if ($b == "1") {
		$beginchar = "1";
		$wherepart = "COALESCE(alias.label, sce.title) REGEXP '^[^a-zæøå]'";
	} elseif (in_array($b,$chars)) {
		$beginchar = $b;
		$wherepart = "COALESCE(alias.label, sce.title) LIKE '$b%'";
	} else {
		$beginchar = "a";
		$wherepart = "COALESCE(alias.label, sce.title) LIKE 'a%'";
	}
	if ($wherepart) {
		$wherepart = "WHERE ".$wherepart;
	}
}
if ($wherepart) {
	$wherepart .= " AND sce.boardgame = 0";
} else {
	$wherepart = "WHERE sce.boardgame = 0";
}

// Find all games, including persons and cons - restrict to one premiere convent
$r = getall("
	SELECT aut.id AS autid, CONCAT(aut.firstname,' ',aut.surname) AS autname, sce.id, sce.title, sce.boardgame, convent.id AS convent_id, convent.name AS convent_name, convent.year, convent.begin, convent.end, convent.cancelled, COUNT(files.id) AS files, COALESCE(alias.label, sce.title) AS title_translation
	FROM sce
	LEFT JOIN csrel ON sce.id = csrel.sce_id AND csrel.pre_id = 1
	LEFT JOIN convent ON csrel.convent_id = convent.id
	LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id = 1
	LEFT JOIN aut ON asrel.aut_id = aut.id
	LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
	LEFT JOIN alias ON sce.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	$wherepart
	GROUP BY csrel.pre_id,csrel.sce_id,asrel.aut_id, sce.id, convent.id
	ORDER BY title_translation, aut.surname, aut.firstname, convent.year, convent.begin, convent.end
");

$last_sce_id = 0;
$xscenlist = "";

// byg scenarieliste på forhånd
$scenarios = [];
foreach ($r AS $row) {
	$sce_id = $row['id'];
	$scenarios[$sce_id]['title'] = $row['title_translation'];
	$scenarios[$sce_id]['origtitle'] = $row['title'];
	$scenarios[$sce_id]['boardgame'] = $row['boardgame'];
	$scenarios[$sce_id]['aut'][$row['autid']] = [ 'name' => $row['autname'] ];
#	$scenarios[$sce_id]['con'][$row['convent_id']] = [ 'name' => $row['convent_name'], 'year' => $row['year'] ];
	$scenarios[$sce_id]['con'][$row['convent_id']] = [ 'id' => $row['convent_id'], 'name' => $row['convent_name'], 'year' => $row['year'], 'cancelled' => $row['cancelled'], 'begin' => $row['begin'], 'end' => $row['end'] ];
	$scenarios[$sce_id]['downloadable'] = ($row['files'] > 0);
}

foreach($scenarios AS $scenario_id => $scenario) {
	$xscenlist .= "\t<tr>\n";
	if ($_SESSION['user_id']) {
		if ($row['boardgame'] ) {
			$options = getuserlogoptions('boardgame');
		} else {
			$options = getuserlogoptions('scenario');
		}

		foreach( $options AS $type) {
			$xscenlist .= "<td>";
			if ($type != NULL) {
				$xscenlist .= getdynamicscehtml($scenario_id,$type,$userlog[$scenario_id][$type] ?? FALSE);
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
	$xscenlist .= "\t\t<td><a href=\"data?scenarie=" . $scenario_id . "\" class=\"scenarie\" title=\"" . htmlspecialchars($scenario['origtitle']) . "\">" . htmlspecialchars($scenario['title']) . "</a></td>\n";

	// authors
	$xscenlist .= "\t\t<td>";
	foreach ($scenario['aut'] AS $aut_id => $person) {
		if ($aut_id) {
			$xscenlist .= "<a href=\"data?person=" . $aut_id . "\" class=\"person\">" . htmlspecialchars($person['name']) . "</a><br>\n";
		}
	}
	$xscenlist .= "</td>\n";
	
	// convents
	$xscenlist .= "\t\t<td>";
	foreach ($scenario['con'] AS $con_id => $convent) {
		if ($con_id) {
			$xscenlist .= smarty_function_con( $convent ) . "<br>" ;
		}
	}
	$xscenlist .= "</td>\n";
	$xscenlist .= "\t</tr>\n";	
}

// achievements
if ($b == 'c') award_achievement(73); // Scenario beginning with C
if ($g == 9)   award_achievement(74); // Scenario in Thriller genre

$t->assign('keys',$keys);
$t->assign('genre',$genre);
$t->assign('scenlist',$xscenlist);
$t->assign('beginchar',$beginchar);
$t->display('games.tpl');
?>
