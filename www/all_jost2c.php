<?php
#require_once("adm_anmeld.inc");

require_once("./connect.php");
$header = '<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="'.$_SERVER['PHP_SELF'].'" style="text-decoration: none">Six Degrees of Jost L. Hansen...</a></i></font>
</p>';
#require("page.inc");

$lookforwarddays = 365;

/*
$query = "
	SELECT
		(
			IF (
				(DAYOFYEAR(birth) < DAYOFYEAR(CURRENT_DATE)),
				DAYOFYEAR(birth)+DAYOFYEAR(CONCAT(YEAR(CURRENT_DATE),'-12-31')), DAYOFYEAR(birth)
			) - DAYOFYEAR(CURRENT_DATE)
		) AS sorting,
		id,
		name,
		birth,
		IF (
			(DAYOFYEAR(birth) < DAYOFYEAR(CURRENT_DATE)),
			(YEAR(CURRENT_DATE)-YEAR(birth)+1),
			(YEAR(CURRENT_DATE)-YEAR(birth))
		)
		AS age
	FROM
		aut
	WHERE
		birth
	HAVING
		sorting < $lookforwarddays
	ORDER BY
		sorting ASC
";
*/

$query = "
	SELECT
		(
			IF (
				(RIGHT(birth,5) < RIGHT(CURRENT_DATE,5) ),
				TO_DAYS( CONCAT(YEAR(NOW())+1,RIGHT(birth,6)) ) - TO_DAYS(NOW()),
				TO_DAYS( CONCAT(YEAR(NOW()),RIGHT(birth,6)) ) - TO_DAYS(NOW())
			)
		) AS sorting,
		id,
		CONCAT(firstname,' ',surname) AS name,
		birth,
		IF (
			(RIGHT(birth,5) < RIGHT(CURRENT_DATE,5)),
			(YEAR(CURRENT_DATE)-YEAR(birth)+1), (YEAR(CURRENT_DATE)-YEAR(birth))
		)
		AS age,
		RIGHT(birth,5) AS h1,
		RIGHT(CURRENT_DATE,5) AS h2
	FROM
		aut
	WHERE
		birth
	HAVING
		sorting < $lookforwarddays
	ORDER BY
		sorting ASC
";




$bcheck = mysql_query($query);

print mysql_error();

printf("<b>".mysql_num_rows($bcheck)." %s indenfor de næste $lookforwarddays dage har fødselsdag:</b>\n<br>\n",(mysql_num_rows($bcheck)==1?"person":"personer"));

/*
while ($r = mysql_fetch_array($bcheck)) {
	list($y,$m,$d) = explode("-",$r[birth]);
	$secsleft = mktime(0,0,0,$m,$d,date("Y")) - date("U");
	$daysleft = intval($secsleft / (60*60*24)) + 1;
	if ($secsleft < 0) $daysleft = 0;
	$age = date("Y") - $y;
	if ($daysleft == 0)     $daytext = "i dag! <BLINK><B>Hurra!</B></BLINK>";
	elseif ($daysleft == 1) $daytext = "i morgen!";
	else                    $daytext = "om $daysleft dage.";
	print "<a href=\"/data?person=$r[id]\">$r[name]</a> bliver $age år $daytext<br>\n";
}
*/

while ($r = mysql_fetch_array($bcheck)) {
#	printf("<a href=\"/data?person=$r[id]\">$r[name]</a> bliver $r[age] år om $r[sorting] %s<br>\n",($r[sorting]==1?"dag":"dage"));
	if ($r['age'] % 5 == 0) {
		print "<b>";
	}
	printf("$r[name] bliver $r[age] år om $r[sorting] %s<br>\n",($r[sorting]==1?"dag":"dage"));
	if ($r['age'] % 5 == 0) {
		print "</b>";
	}
#	print "($r[antalaar])<br>";
}

print "<hr>";

mysql_data_seek($bcheck,0);

print "<table border=1>";
print "<tr><th>sorting</th><th>id</th><th>name</th><th>birth</th><th>age</th><th>h1</th><th>h2</th></tr>\n";
while ($r = mysql_fetch_row($bcheck)) {
	print "<tr>";
	foreach($r AS $value) {
		print "<td>$value</td>";
	}
	print "</tr>";
}
print "</table>";

print "<hr>";

print "<pre>$query</pre>\n";
	
?>
