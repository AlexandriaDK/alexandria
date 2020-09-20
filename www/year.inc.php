<?php
$this_type = 'year';

list($startyear,$endyear) = getrow("SELECT MIN(year), MAX(year) FROM convent");

$yearlist = "";

for ($i = $startyear; $i <= $endyear; $i++) {
	if ($i == $year) $yearlist .= "<b>$i</b>";
	else $yearlist .= "<a href=\"data?year=$i\">$i</a>";
	if ($i < $endyear) {
		if ($i % 10 == 0) {
			$yearlist .= "<br>";
		} else {
			$yearlist .= " - ";
		}
	}
}

$yearlist = "<table>\n";
for ($i = floor(($startyear-1)/10)*10+1; $i <= $endyear; $i++) {
	if (($i-1)%10 == 0) $yearlist .= "<tr>";

	if ($i < $startyear) $yearlist .= "<td></td>";
	elseif ($i == $year) $yearlist .= "<td>" . yearname( $i ) . "</td>";
	else $yearlist .= "<td><a href=\"data?year=$i\" class=\"con\">" . yearname( $i ) ."</a></td>";
	
	if ($i % 10 == 0 || $i == $endyear) $yearlist .= "</tr>\n";
}
$yearlist .= "</table>";

$output = "";
$q = getall("
	(
		SELECT 'convent' AS type, convent.id, convent.name, convent.year, convent.description, begin, end, place, conset_id, conset.name AS cname, cancelled, convent.name AS origname
		FROM convent
		LEFT JOIN conset ON convent.conset_id = conset.id
		WHERE year = '$year'
	)
	UNION
	(
		SELECT 'sce' AS type, sce.id, COALESCE(alias.label, sce.title) AS name, YEAR(scerun.begin) AS year, sce.description, scerun.begin, scerun.end, scerun.location, sce.id AS conset_id, sce.title AS cname, scerun.cancelled, sce.title AS origname
		FROM scerun
		INNER JOIN sce ON scerun.sce_id = sce.id
		LEFT JOIN alias ON sce.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
		WHERE scerun.begin BETWEEN '$year-00-00' AND '$year-12-31'
	)
	ORDER BY begin, end, name
");
$num_cons = count($q);
$thismonth = -1;
$timeinfo = "";
foreach($q AS $row) {
	$month = (int) substr($row['begin'],5,2);
	if ($month > $thismonth) {
		$printmonth = ucfirst( monthname( intval($month) ) );
		if ($month == 0) $printmonth = htmlspecialchars($t->getTemplateVars('_year_unknowndate'));
		if ($output) $output .= "</p>" . PHP_EOL;
		$output .= "<h3 class=\"calendarhead\">$printmonth</h3>" . PHP_EOL . "<p class=\"calendarmonth\">" . PHP_EOL;
		$thismonth = $month;
	}

#		$coninfo = nicedateset($row['begin'],$row['end']);
#		$coninfo = intval(substr($row['begin'],8,2)).".-".intval(substr($row['end'],8,2)).".";
	if ($month != 0) {
		if (substr($row['begin'],8,2) == "00") {
			$timeinfo = htmlspecialchars($t->getTemplateVars('_year_unknowndate'));
		} elseif ($row['begin'] == $row['end'] || !$row['end']) {
			$timeinfo = specificdate( $row['begin'] );
		} else {
			$timeinfo = specificdate( $row['begin'] ) . "-" . specificdate( $row['end'] );
		}
		$timeinfo .= " ";
		
	}
	if ($row['cancelled']) {
		$output .= "<span class=\"cancelled\">";
	}
	if ($row['type'] == 'convent') {
		unset($row['year']);
		$output .= " " . $timeinfo . smarty_function_con($row) . "<br>\n";
	} elseif ($row['type'] == 'sce') {
		$output .= " $timeinfo<a href=\"data?scenarie={$row['id']}\" class=\"scenarie\">{$row['name']}</a><br>\n";
	}
	if ($row['cancelled']) {
		$output .= "</span>";
	}
}
if ($output) {
	$output .= "</p>" . PHP_EOL;
}

$t->assign('pagetitle',$year);
$t->assign('type',$this_type);

$t->assign('startyear',$startyear);
$t->assign('endyear',$endyear);
$t->assign('year',$year);
$t->assign('yearlist',$yearlist);
$t->assign('num_cons',$num_cons);
$t->assign('output',$output);
#	$t->assign('pagetitle',"$r['name']." ({$r['year']})");
#	$t->assign('type',$this_type);

$t->display('data.tpl');
?>
