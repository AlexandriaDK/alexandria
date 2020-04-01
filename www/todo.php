<?php
require("./connect.php");
require("base.inc");

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

$cons_list    = conListByConfirmed(1);
$cons_list_c = conListCountries($cons_list);
$cons_content = conListByConfirmed(3);
$cons_content_c = conListCountries($cons_content);
$cons_missing = conListByConfirmed(0);
$cons_missing_c = conListCountries($cons_missing);

$t->assign('cons_list', $cons_list);
$t->assign('cons_list_c', $cons_list_c);
$t->assign('cons_content', $cons_content);
$t->assign('cons_content_c', $cons_content_c);
$t->assign('cons_missing', $cons_missing);
$t->assign('cons_missing_c', $cons_missing_c);
$t->display('todo.tpl');
?>
