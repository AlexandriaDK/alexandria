<?php
require("./connect.php");
require("base.inc");
require("template.inc");

$joinpart = "";

// Find alle brætspil inkl. personer og cons
if ($_SESSION['user_id']) {
	$r = getall("
		SELECT aut.id AS autid, CONCAT(aut.firstname,' ',aut.surname) AS autname, sce.id, sce.title, sce.boardgame, convent.id AS convent_id, convent.name AS convent_name, convent.year, SUM(type = 'read') AS `read`, SUM(type = 'gmed') AS gmed, SUM(type = 'played') AS played, COUNT(files.id) AS files
		FROM sce
		LEFT JOIN csrel ON sce.id = csrel.sce_id AND csrel.pre_id = 1
		LEFT JOIN convent ON csrel.convent_id = convent.id
		LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id IN (1,5)
		LEFT JOIN aut ON asrel.aut_id = aut.id
		LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
		LEFT JOIN userlog ON sce.id = userlog.data_id AND userlog.category = 'sce' AND userlog.user_id = '{$_SESSION['user_id']}'
		$joinpart
		WHERE sce.boardgame = 1
		GROUP BY csrel.pre_id,csrel.sce_id,asrel.aut_id, sce.id, convent.id
		ORDER BY title, aut.surname, aut.firstname
	");
} else {
	$r = getall("
		SELECT aut.id AS autid, CONCAT(aut.firstname,' ',aut.surname) AS autname, sce.id, sce.title, sce.boardgame, convent.id AS convent_id, convent.name AS convent_name, convent.year, COUNT(files.id) AS files
		FROM sce
		LEFT JOIN csrel ON sce.id = csrel.sce_id AND csrel.pre_id = 1
		LEFT JOIN convent ON csrel.convent_id = convent.id
		LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id IN (1,5)
		LEFT JOIN aut ON asrel.aut_id = aut.id
		LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
		$joinpart
		WHERE sce.boardgame = 1
		GROUP BY csrel.pre_id,csrel.sce_id,asrel.aut_id, sce.id, convent.id
		ORDER BY title, aut.surname, aut.firstname
	");
}

$last_sce_id = 0;
$scenlist = "";

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
		//$scenlist .= '<td><img src="/gfx/ikon_download.gif" alt="Download" title="Dette scenarie kan downloades" width="15" height="15" /></td>';
//		$scenlist .= "<td><span title=\"Regler til dette brætspil kan downloades\">💾</span></td>";
		$scenlist .= "<td><span title=\"Regler til dette brætspil kan downloades\"><a href=\"data?scenarie=" . $sce_id . "\">💾</a></span></td>";
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

$t->assign('ip',$_SERVER['REMOTE_ADDR']);
// $t->assign('genre',$genre);
$t->assign('scenlist',$scenlist);
$t->assign('boardgamesonly',TRUE);
$t->display('games.tpl');

?>
