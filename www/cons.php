<?php
require("./connect.php");
require("base.inc");

if ($_SESSION['user_id']) {
	$result = getall("
		SELECT convent.id, convent.name, convent.conset_id AS setid, conset.name AS setname, convent.year, convent.begin, convent.end, convent.cancelled, userlog.type IS NOT NULL AS visited
		FROM convent
		LEFT JOIN userlog ON
			convent.id = userlog.data_id AND
			userlog.category = 'convent' AND
			userlog.user_id = '{$_SESSION['user_id']}'
		LEFT JOIN conset ON convent.conset_id = conset.id
		ORDER BY conset.id = 40, conset.name, convent.year, convent.begin, convent.end, name
	");
} else {
	$result = getall("
		SELECT convent.id, convent.name, convent.conset_id AS setid, conset.name AS setname, convent.year, convent.begin, convent.end, convent.cancelled
		FROM convent
		LEFT JOIN conset ON convent.conset_id = conset.id
		ORDER BY conset.id = 40, conset.name, convent.year, convent.begin, convent.end, name
	");
}

$conset = "";
$part = 1;

$list = "";

foreach($result AS $r) {
	if ($conset != $r['setid']) {
		if ($r['setid'] == 40) {
			$setid = 40;
			$setname = $t->getTemplateVars('_cons_other');
		} else {
			$setid = $r['setid'];
			$setname = $r['setname'];
			
		}
		if ($conset != "") {
			$list .= "</div>\n";
		}
		$list .= "<div class=\"conblock\">";
		$conset = $r['setid'];
		$list .= "<h3 style=\"display: inline;\"><a href=\"data?conset=$setid\">" . htmlspecialchars($setname) . "</a></h3><br />\n";
	}
	$coninfo = nicedateset($r['begin'],$r['end']);
	if ($_SESSION['user_id']) {
		$list .= getdynamicconventhtml($r['id'],'visited',$r['visited']);
	} else {
		$list .= "&nbsp;&nbsp;";
	}
	
	$list .= smarty_function_con( $r ) . "<br>";
}
$list .= "</div>";

// Smarty
$t->assign('list',$list);

$t->display('convents.tpl');

?>
