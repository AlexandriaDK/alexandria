<?php
require("./connect.php");
require("base.inc");
require("template.inc");

if ($_SESSION['user_id']) {
	$result = getall("
		SELECT convent.id, COALESCE(CONCAT(convent.name,' (',convent.year,')'), convent.name) AS conname, conset.id AS setid, conset.name AS setname, convent.begin, convent.end, convent.cancelled, userlog.type IS NOT NULL AS visited
		FROM convent
		LEFT JOIN userlog ON
			convent.id = userlog.data_id AND
			userlog.category = 'convent' AND
			userlog.user_id = '{$_SESSION['user_id']}'
		LEFT JOIN conset ON convent.conset_id = conset.id
		ORDER BY conset.name, convent.year, convent.begin, convent.end, conname
	");
} else {
	$result = getall("
		SELECT convent.id, COALESCE(CONCAT(convent.name,' (',convent.year,')'), convent.name) AS conname, conset.id AS setid, conset.name AS setname, convent.begin, convent.end, convent.cancelled
		FROM convent
		LEFT JOIN conset ON convent.conset_id = conset.id
		ORDER BY conset.name, convent.year, convent.begin, convent.end, conname
	");
}

$conset = "";
$part = 1;

$list = "";

foreach($result AS $r) {
	if ($conset != $r['setid']) {
		/*
		if ($part == 1 && substr($r['setname'],0,1) >= "K") {
			$part = 2;
		}
		*/
		if ($conset != "") {
			$list .= "</div>\n";
		}
		$list .= "<div class=\"conblock\">";
		$conset = $r['setid'];
		$list .= "<h3 style=\"display: inline;\"><a href=\"data?conset={$r['setid']}\">".htmlspecialchars($r['setname'])."</a></h3><br />\n";
	}
	$coninfo = nicedateset($r['begin'],$r['end']);
	if ($_SESSION['user_id']) {
		$list .= getdynamicconventhtml($r['id'],'visited',$r['visited']);
	} else {
		$list .= "&nbsp;&nbsp;";
	}
	
	$list .= "<a href=\"data?con={$r['id']}\" title=\"$coninfo\" " . ($r['cancelled'] ? "class=\"cancelled\"" : "") . ">".htmlspecialchars($r['conname'])."</a><br />\n";
}
$list .= "</div>";

// Smarty
#$t->assign('part1',$data[1]);
#$t->assign('part2',$data[2]);
$t->assign('list',$list);

$t->display('convents.tpl');

?>
