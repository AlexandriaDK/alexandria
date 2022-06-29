<?php
require("./connect.php");
require("base.inc.php");


if ($_SESSION['user_id']) {
	$userlog = getuserlogconvents($_SESSION['user_id']);
}

$result = getall("
	SELECT c.id, c.name, c.conset_id AS setid, conset.name AS setname, c.year, c.begin, c.end, c.cancelled, COALESCE(c.country, conset.country) AS country
	FROM convention c
	LEFT JOIN conset ON c.conset_id = conset.id
	ORDER BY conset.id = 40, conset.name, c.year, c.begin, c.end, name
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
// PHP 8.0 Smarty workaround, as implode now requires separator string as first argument
foreach($cons AS $id => $con) {
	$cons[$id]['countrieslist'] = array_keys($cons[$id]['countries']);
}
arsort( $countries, SORT_NUMERIC );
$countries = array_keys( $countries );



// Smarty
$t->assign('cons',$cons);
$t->assign('countries',$countries);

$t->display('convents.tpl');

?>
