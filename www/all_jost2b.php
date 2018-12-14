<?php
require("./connect.php");
require("./base.inc");
require("template.inc");

$time = [];
$gem = [];
$time['start'] = time();

$person = getcolid("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY id");

foreach ($person AS $key => $value) {

	$check = [];
	$checked = [];

	$mainperson = $key;

	$check[1][] = $mainperson;
	$checked[] = $mainperson;
	$i = 1;
	$personerialt = 1;
	
	// STARTKODE FOR LØKKE
	// running in circles!
	
	while($check[$i]) {
	
		$inlist = join(",",$check[$i]);	
		$notlist = join(",",$checked);
		$query = "
			SELECT
				t2.aut_id AS link,
				sce.title,
				t2.tit_id,
				t1.aut_id AS rlink,
				t1.tit_id AS rtit_id
			FROM
				asrel AS t1,
				sce,
				asrel AS t2
			WHERE
				t1.aut_id IN ($inlist) AND
				sce.id = t1.sce_id AND
				t1.sce_id = t2.sce_id AND
				t2.aut_id NOT IN ($notlist) AND
				t1.tit_id = 1 AND t2.tit_id = 1
			GROUP BY
				link
		";

		$q = getall($query);
		$qnums++;
		foreach($q as $row) {
			$personerialt++;
#			if ($key == 1) print $person[$row['link']]."<br>\n";
			$check[($i+1)][] = $row['link'];
			$checked[] = $row['link'];
		}
		$i++;
#		if ($key == 1) print "<br>";
	}
	
	// SLUTKODE FOR LØKKE

	$led = $i-2;

	if ($led > $maxled['antal']) {
		$maxled['antal'] = $led;
		$maxled['personid'] = $key;
	}

	print "<a href=\"/all_jost?from=$key\">{$person[$mainperson]}</a>: $personerialt [over $led led]<br>\n";
	flush();
	$gem[$personerialt]++;
}

$time['end'] = time();

print "<hr>";

krsort($gem);
#$gem = array_reverse($gem);


print "<pre>";

print "Max led: $maxled[antal] af ".$person[$maxled['personid']]."\n\n";

print "Queries i alt: $qnums\n";
print "Samlet beregningstid: ".($time['end']-$time['start'])." sekunder\n\n";

foreach($gem AS $key => $value) {
	print "Antal ".$key."-personers-kæder: ".($value/$key)."\n";
}
print "\n";

print_r($gem);
print "</pre>";
	
?>
