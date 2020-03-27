<?php
require("./connect.php");
require("base.inc");

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
	$keys .= "\n\t\t<a href=\"scenarier?b=".rawurlencode($char)."\">".mb_strtoupper($char == "1" ? "0-9" : $char)."</a>";
}

// fetch genres
$genre = array();
$genres = getcolid("SELECT gen.id, gen.name FROM gen ORDER BY gen.name");
foreach($genres AS $gid => $gname) {
	#$genre[] = '<a href="'.$_SERVER['PHP_SELF'].'?g='.$g['id'].'">'.htmlspecialchars($g['name']).'</a>';
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
	if (isset($genres[$g])) {
		$titlepart = $genres[$g];
	} else {
		$titlepart = "Ukendt kategori";
	}
} else {
	if ($b == "1") {
		$beginchar = "1";
		$titlepart = "Begynder med tal eller specialtegn";	
		$wherepart = "sce.title REGEXP '^[^a-zæøå]'";
	} elseif (in_array($b,$chars)) {
		$beginchar = $b;
		$titlepart = "Begynder med bogstavet $b";
		$wherepart = "sce.title LIKE '$b%'";
	} else {
		$beginchar = "a";
		$titlepart = "Begynder med bogstavet a";
		$wherepart = "sce.title LIKE 'a%'";
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

// Find alle scenarier, inkl. personer og cons - medtag dog kun én premierecon.
// ... havde før i tiden "USE INDEX(sce_id)" efter csrel og asrel-joins for at sikre sig
// at den rette key blev brugt, men "ANALYZE TABLE csrel" og "ANALYZE TABLE asrel"
// sørgede for dette i første omgang

if ($_SESSION['user_id']) {
	$r = getall("
		SELECT aut.id AS autid, CONCAT(aut.firstname,' ',aut.surname) AS autname, sce.id, sce.title, sce.boardgame, convent.id AS convent_id, convent.name AS convent_name, convent.year, convent.cancelled, SUM(type = 'read') AS `read`, SUM(type = 'gmed') AS gmed, SUM(type = 'played') AS played, COUNT(files.id) AS files
		FROM sce
		LEFT JOIN csrel ON sce.id = csrel.sce_id AND csrel.pre_id = 1
		LEFT JOIN convent ON csrel.convent_id = convent.id
		LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id = 1
		LEFT JOIN aut ON asrel.aut_id = aut.id
		LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
		LEFT JOIN userlog ON sce.id = userlog.data_id AND userlog.category = 'sce' AND userlog.user_id = '{$_SESSION['user_id']}'
		$wherepart
		GROUP BY csrel.pre_id,csrel.sce_id,asrel.aut_id, sce.id, convent.id
		ORDER BY title, aut.surname, aut.firstname, convent.year, convent.begin, convent.end
	");
} else {
	$r = getall("
		SELECT aut.id AS autid, CONCAT(aut.firstname,' ',aut.surname) AS autname, sce.id, sce.title, sce.boardgame, convent.id AS convent_id, convent.name AS convent_name, convent.year, convent.cancelled, COUNT(files.id) AS files
		FROM sce
		LEFT JOIN csrel ON sce.id = csrel.sce_id AND csrel.pre_id = 1
		LEFT JOIN convent ON csrel.convent_id = convent.id
		LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id = 1
		LEFT JOIN aut ON asrel.aut_id = aut.id
		LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
		$wherepart
		GROUP BY csrel.pre_id,csrel.sce_id,asrel.aut_id, sce.id, convent.id
		ORDER BY title, aut.surname, aut.firstname, convent.year, convent.begin, convent.end
	");
}

$last_sce_id = 0;
$scenlist = "";
$xscenlist = "";

// byg scenarieliste på forhånd
$scenarios = [];
foreach ($r AS $row) {
	$sce_id = $row['id'];
	$scenarios[$sce_id]['title'] = $row['title'];
	$scenarios[$sce_id]['boardgame'] = $row['boardgame'];
	$scenarios[$sce_id]['aut'][$row['autid']] = [ 'name' => $row['autname'] ];
#	$scenarios[$sce_id]['con'][$row['convent_id']] = [ 'name' => $row['convent_name'], 'year' => $row['year'] ];
	$scenarios[$sce_id]['con'][$row['convent_id']] = [ 'name' => $row['convent_name'], 'year' => $row['year'], 'cancelled' => $row['cancelled'] ];
	$scenarios[$sce_id]['downloadable'] = ($row['files'] > 0);
	if ($_SESSION['user_id']) {
		$scenarios[$sce_id]['userdata'] = ['read' => $row['read'], 'gmed' => $row['gmed'], 'played' => $row['played'] ]; 
	}
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
				$xscenlist .= getdynamicscehtml($row['id'],$type,$scenario['userdata'][$type]);
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
	$xscenlist .= "\t\t<td><a href=\"data?scenarie=" . $scenario_id . "\" class=\"scenarie\">".htmlspecialchars($scenario['title'])."</a></td>\n";

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
			$class = "con";
			if ($convent['cancelled'] == 1) {
				$class .= " cancelled";
			}
			$xscenlist .= "<a href=\"data?con=" . $con_id . "\" class=\"$class\">".htmlspecialchars($convent['name'])." (" . $convent['year'] . ")</a><br>\n";
		}
	}
	$xscenlist .= "</td>\n";
	$xscenlist .= "\t</tr>\n";	
}

/*
foreach($r AS $row) {
	$sce_id = $row['id'];

	$scenlist .= "\t<tr class=\"listresult\">\n";
	if ($_SESSION['user_id']) {
		if ($sce_id != $last_sce_id) {
			if ($row['boardgame'] ) {
				$options = getuserlogoptions('boardgame');
			} else {
				$options = getuserlogoptions('scenario');
			}

			foreach( $options AS $type) {
				$scenlist .= "<td>";
				if ($type != NULL) {
					$scenlist .= getdynamicscehtml($row['id'],$type,$row[$type]);
				}
				$scenlist .= "</td>";
			}
			$scenlist .= "<td style=\"width: 10px;\">&nbsp;</td>";
		} else {
			$scenlist .= "<td colspan=\"4\"></td>";
		}
	}

	if ($sce_id != $last_sce_id && $row['files'] > 0) {
//		$scenlist .= '<td><img src="/gfx/ikon_download.gif" alt="Download" title="Dette scenarie kan downloades" width="15" height="15" /></td>';
		$scenlist .= "<td><span title=\"" . htmlspecialchars($t->getTemplateVars('_sce_downloadable')) . "\">💾</span></td>";
	} else {
		$scenlist .= "<td></td>";
	}
	if ($sce_id != $last_sce_id) {
		$scenlist .= "\t\t<td><a href=\"data?scenarie={$sce_id}\" class=\"scenarie\">".htmlspecialchars($row['title'])."</a></td>\n";
	} else {
		$scenlist .= "\t\t<td>&nbsp;</td>\n";
	}

	if ($row['autid']) {
		$scenlist .= "\t\t<td><a href=\"data?person={$row['autid']}\" class=\"person\">{$row['autname']}</a></td>\n";
	} else {
		$scenlist .= "\t\t<td>&nbsp;</td>\n";
	}

	if ($sce_id != $last_sce_id && $row['convent_id']) {
		$scenlist .= "\t\t<td><a href=\"data?con={$row['convent_id']}\" class=\"con\">".htmlspecialchars($row['convent_name'])." ({$row['year']})</a></td>\n";
	} else {
		$scenlist .= "\t\t<td>&nbsp;</td>\n";
	}

	$scenlist .= "\t</tr>\n";
	$last_sce_id = $sce_id;
}
*/

// achievements
if ($b == 'c') award_achievement(73); // Scenario beginning with C
if ($g == 9)   award_achievement(74); // Scenario in Thriller genre

$t->assign('keys',$keys);
$t->assign('genre',$genre);
$t->assign('scenlist',$xscenlist);
$t->assign('titlepart',$titlepart);
$t->assign('beginchar',$beginchar);
$t->display('games.tpl');

?>
