<?php
require("./connect.php");
$header = '<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="'.$_SERVER['PHP_SELF'].'" style="text-decoration: none">Six Degrees of Jost L. Hansen...</a></i></font>
</p>';
#require("page.inc");

$t['start'] = time();

unset($person);
unset($qnums);
unset($maxled);

$q = mysql_query("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY id");
while ($row = mysql_fetch_row($q)) $person[$row[0]] = $row[1];

foreach ($person AS $key => $value) {

	unset($check);
	unset($checked);

	$mainperson = $key;
	$hest++;

#	if ($hest == 20) break;

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

		$q = mysql_query($query);
		$qnums++;
		while ($row = mysql_fetch_array($q)) {
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

$t['end'] = time();

print "<hr>";

krsort($gem);
#$gem = array_reverse($gem);


print "<pre>";

print "Max led: $maxled[antal] af ".$person[$maxled['personid']]."\n\n";

print "Queries i alt: $qnums\n";
print "Samlet beregningstid: ".($t['end']-$t['start'])." sekunder\n\n";

foreach($gem AS $key => $value) {
	print "Antal ".$key."-personers-kæder: ".($value/$key)."\n";
}
print "\n";

print_r($gem);
print "</pre>";
	
?>
