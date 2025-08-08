<?php
$this_type = 'year';

list($conventionminyear, $conventionmaxyear) = getrow("SELECT MIN(year), MAX(year) FROM convention");
list($gamerunminyear, $gamerunmaxyear) = getrow("SELECT MIN(YEAR(begin)), MAX(YEAR(begin)) FROM gamerun");

// Normalize to integers and handle potential NULL/0 values from the DB
$cmin = (int) ($conventionminyear ?: 0);
$gmin = (int) ($gamerunminyear ?: 0);
$cmax = (int) ($conventionmaxyear ?: 0);
$gmax = (int) ($gamerunmaxyear ?: 0);

// Compute bounds from whichever source has data
if ($cmin && $gmin) {
  $startyear = min($cmin, $gmin);
} elseif ($cmin) {
  $startyear = $cmin;
} else {
  $startyear = $gmin;
}
$endyear = max($cmax, $gmax);

// Clamp to the supported/desired range: from 1970 to the latest year with an event
$startyear = max(1970, (int) $startyear);
$endyear = max($startyear, (int) $endyear);

$yearlist = "";

for ($i = $startyear; $i <= $endyear; $i++) {
  if ($i == $year) {
    $yearlist .= "<b>$i</b>";
  } else {
    $yearlist .= "<a href=\"data?year=$i\">$i</a>";
  }
  if ($i < $endyear) {
    if ($i % 10 == 0) {
      $yearlist .= "<br>";
    } else {
      $yearlist .= " - ";
    }
  }
}

$yearlist = "<table>\n";
$gridStart = 1970; // Show calendar grid from 1970 up to the latest event year
for ($i = $gridStart; $i <= $endyear; $i++) {
  if ((($i - 1) % 10) == 0) {
    $yearlist .= "<tr>";
  }

  if ($i < $startyear) {
    $yearlist .= "<td></td>";
  } elseif ($i == $year) {
    $yearlist .= "<td>" . yearname($i) . "</td>";
  } else {
    $yearlist .= "<td><a href=\"data?year=$i\" class=\"con\">" . yearname($i) . "</a></td>";
  }

  if (($i % 10) == 0 || $i == $endyear) {
    $yearlist .= "</tr>\n";
  }
}
$yearlist .= "</table>";

$output = "";
$q = getall("
	(
		SELECT 'convention' AS type, c.id, c.name, c.year, c.description, begin, end, place, conset_id, conset.name AS cname, cancelled, c.name AS origname
		FROM convention c
		LEFT JOIN conset ON c.conset_id = conset.id
		WHERE year = '$year'
	)
	UNION
	(
		SELECT 'game' AS type, g.id, COALESCE(alias.label, g.title) AS name, YEAR(gr.begin) AS year, g.description, gr.begin, gr.end, gr.location, g.id AS conset_id, g.title AS cname, gr.cancelled, g.title AS origname
		FROM gamerun gr
		INNER JOIN game g ON gr.game_id = g.id
		LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
		WHERE gr.begin BETWEEN '$year-00-00' AND '$year-12-31'
	)
	ORDER BY begin, end, name
");
$num_cons = count($q);
$thismonth = -1;
$timeinfo = "";
foreach ($q as $row) {
  $begin = $row['begin'] ?? '';
  $month = (int) substr($begin, 5, 2);
  if ($month > $thismonth) {
    $printmonth = ucfirst(monthname(intval($month)));
    if ($month == 0) {
      $printmonth = htmlspecialchars($t->getTemplateVars('_year_unknowndate'));
    }
    if ($output) {
      $output .= "</p></div>" . PHP_EOL;
    }
    $output .= "<div><h3 class=\"calendarhead\">$printmonth</h3>" . PHP_EOL . "<p class=\"calendarmonth\">" . PHP_EOL;
    $thismonth = $month;
  }



  if ($month != 0) {
    if (substr($row['begin'], 8, 2) == "00") {
      $timeinfo = htmlspecialchars($t->getTemplateVars('_year_unknowndate'));
    } elseif ($row['begin'] == $row['end'] || !$row['end']) {
      $timeinfo = specificdate($row['begin']);
    } else {
      $timeinfo = specificdate($row['begin']) . "-" . specificdate($row['end']);
    }
    $timeinfo .= " ";
  }
  if ($row['cancelled']) {
    $output .= "<span class=\"cancelled\">";
  }
  if ($row['type'] == 'convention') {
    unset($row['year']);
    $output .= " " . $timeinfo . smarty_function_con($row) . "<br>\n";
  } elseif ($row['type'] == 'game') {
    $output .= " $timeinfo<a href=\"data?scenarie={$row['id']}\" class=\"game\">{$row['name']}</a><br>\n";
  }
  if ($row['cancelled']) {
    $output .= "</span>";
  }
}
if ($output) {
  $output .= "</p></div>" . PHP_EOL;
}

$t->assign('pagetitle', $year);
$t->assign('type', $this_type);

$t->assign('startyear', $startyear);
$t->assign('endyear', $endyear);
$t->assign('year', $year);
$t->assign('yearlist', $yearlist);
$t->assign('num_cons', $num_cons);
$t->assign('output', $output);
#	$t->assign('pagetitle',"$r['name']." ({$r['year']})");
#	$t->assign('type',$this_type);

$t->display('data.tpl');
