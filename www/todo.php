<?php
require("./connect.php");
require("base.inc.php");

function addLocaleCountry($dbresult) {
	foreach ($dbresult AS $id => $data) {
		$dbresult[$id]['localecountry'] = Locale::getDisplayRegion("-" . $data['country'], LANG);
	}
	return $dbresult;

}

function conListByConfirmed($confirmed) {
	$confirmed = (int) $confirmed;
	$list = getall("SELECT convent.id, convent.name, convent.begin, convent.end, convent.year, COALESCE(convent.country, conset.country) AS country FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE confirmed = $confirmed ORDER BY convent.year DESC, convent.name");
	$list = addLocaleCountry($list);
	return $list;
}

function conListByConfirmedGroup($confirmed) {
	$confirmed = (int) $confirmed;
	$result = [];
	$list = getall("SELECT convent.id, convent.name, convent.begin, convent.end, convent.year, COALESCE(convent.country, conset.country) AS country FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE confirmed = $confirmed ORDER BY country, convent.year DESC, convent.name");
	foreach ($list AS $convent) {
		if (!isset($result[$convent['country']]) ) {
			$result[$convent['country']] = [ 'countryname' => getCountryName($convent['country']), 'cons' => [] ];
		}
		$result[$convent['country']]['cons'][] = $convent;
	}
	uasort($result, function($a, $b) { return count($b['cons']) - count($a['cons']); }); // sort array with most cons at top
	return $result;
}

function conListCountries($list) {
	$count = [];
	foreach($list AS $con) {
		if ($con['country'] ?? "") {
			if (!isset($count[$con['country']])) {
				$count[$con['country']] = 0;
			}
			$count[$con['country']]++;
		}
	}
	arsort($count);
	$countries = [];
	foreach($count AS $country => $count) {
		$countries[$country] = getCountryName($country);
	}
	return $countries;
}

$cons_list    = conListByConfirmedGroup(1);
$cons_content = conListByConfirmedGroup(3);
$cons_missing = conListByConfirmedGroup(0);

$t->assign('todo_tabs', TRUE);
$t->assign('cons_list', $cons_list);
$t->assign('cons_content', $cons_content);
$t->assign('cons_missing', $cons_missing);
$t->display('todo.tpl');
?>
