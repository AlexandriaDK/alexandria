<?php
require("./connect.php");
require("base.inc.php");


if ($_SESSION['user_id']) {
	$userlog = getuserlogconvents($_SESSION['user_id']);
}

$result = getall("
	SELECT convent.id, convent.name, convent.conset_id AS setid, conset.name AS setname, convent.year, convent.begin, convent.end, convent.cancelled, COALESCE(convent.country, conset.country) AS country
	FROM convent
	LEFT JOIN conset ON convent.conset_id = conset.id
	ORDER BY conset.id = 40, conset.name, convent.year, convent.begin, convent.end, name
");

$conset = "";
$part = 1;

$list = "";

$cons = [];
$countries = [];

foreach( $result AS $c ) {
	$setid = $c['setid'];
	$conid = $c['id'];
	if ( ! isset( $cons[$setid] ) ) {
		$cons[$setid] = [
			'setname' => $c['setname'],
			'countries' => [],
			'cons' => []
		];
	}
	if ($userlog) {
		$c['userloghtml'] = getdynamicconventhtml($conid, 'visited', in_array($conid, $userlog) );
	}
	$cons[$setid]['cons'][$conid] = $c;
	$cons[$setid]['countries'][$c['country']] = TRUE;
	if ( $c['country'] ) {
		if ( ! isset( $countries[$c['country']] ) ) {
			$countries[$c['country']] = 0;
		}
		$countries[$c['country']]++;
	}
}
arsort( $countries, SORT_NUMERIC );
$countries = array_keys( $countries );

/*
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
			$list .= "\n</div>\n\n";
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
$list .= "\n</div>\n";
 */
//
// Smarty
$t->assign('cons',$cons);
$t->assign('countries',$countries);

$t->display('convents.tpl');

?>
