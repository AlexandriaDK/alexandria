<?php
require("./connect.php");
require("base.inc.php");


if ($_SESSION['user_id']) {
	$userlog = getuserlogconvents($_SESSION['user_id']);
}

$result = getall("
	SELECT c.id, c.name, c.conset_id AS setid, cs.name AS setname, c.year, c.begin, c.end, c.cancelled, COALESCE(c.country, cs.country) AS country, COALESCE(alias.label, cs.name) AS conset_translation
	FROM convention c
	LEFT JOIN conset cs ON c.conset_id = cs.id
	LEFT JOIN alias ON cs.id = alias.conset_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	ORDER BY cs.id = 40, cs.name, c.year, c.begin, c.end, name
");

$conset = "";
$part = 1;

$list = "";

$cons = [];
$countries = [];

foreach ($result as $c) {
	$setid = $c['setid'];
	$conid = $c['id'];
	if (!isset($cons[$setid])) {
		$cons[$setid] = [
			'setname' => $c['conset_translation'],
			'countries' => [],
			'cons' => []
		];
	}
	if ($userlog) {
		$c['userloghtml'] = getdynamicconventionhtml($conid, 'visited', in_array($conid, $userlog));
	}
	$cons[$setid]['cons'][$conid] = $c;
	$cons[$setid]['countries'][$c['country']] = TRUE;
	if ($c['country']) {
		if (!isset($countries[$c['country']])) {
			$countries[$c['country']] = 0;
		}
		$countries[$c['country']]++;
	}
}
// PHP 8.0 Smarty workaround, as implode now requires separator string as first argument
foreach ($cons as $id => $con) {
	$cons[$id]['countrieslist'] = array_keys($cons[$id]['countries']);
}
arsort($countries, SORT_NUMERIC);
$countries = array_keys($countries);

// Smarty
$t->assign('cons', $cons);
$t->assign('countries', $countries);

$t->display('conventions.tpl');
