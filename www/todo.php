<?php
require("./connect.php");
require("base.inc");

function addLocaleCountry($dbresult) {
	foreach ($dbresult AS $id => $data) {
		$dbresult[$id]['localecountry'] = Locale::getDisplayRegion("-" . $data['country'], LANG);
	}
	return $dbresult;

}

$cons_list = getall("SELECT convent.id, convent.name, convent.year, COALESCE(convent.country, conset.country) AS country FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE confirmed = 1 ORDER BY convent.year DESC, convent.name");
$cons_list = addLocaleCountry($cons_list);

$cons_content = getall("SELECT convent.id, convent.name, convent.year, COALESCE(convent.country, conset.country) AS country FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE confirmed = 3 ORDER BY convent.year DESC, convent.name");
$cons_content = addLocaleCountry($cons_content);

$t->assign('cons_list', $cons_list);
$t->assign('cons_content', $cons_content);
$t->display('todo.tpl');
?>
