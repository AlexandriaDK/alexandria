<?php
require_once("template.inc");
require_once("./connect.php");
/*
$header = '<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="'.$_SERVER['PHP_SELF'].'" style="text-decoration: none">Six Degrees...</a></i></font>
</p>';
*/
#include("page.inc");

print pagebegin("Jost-spillet");
print pagehead("Six Degrees...");

$match = intval($_REQUEST['from']);
if ($_REQUEST['person']) $match = intval($_REQUEST['person']);
$mainperson = $match;

#$mainperson = 51;

?>
<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=0 align=center><TR><TD BGCOLOR="#C000FB">

<table border=0 cellspacing=0 cellpadding=2 align=center bgcolor="white" width=600>
<tr valign=top><td>

<form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
Vælg person:
<select name="from">
<?php
$q = mysql_query("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY name");
while ($r = mysql_fetch_array($q)) {
	print "<option value=\"{$r['id']}\"".($r['id']==$match?" selected":"").">{$r['name']}\n";
}
?>
</select><input type="submit" value="Forbind!">
</form>

<?php
if ($match) {

	unset($person);
	unset($check);
	unset($qnums);
	
	$q = mysql_query("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut");
	while ($row = mysql_fetch_row($q)) $person[$row[0]] = $row[1];
	
	$q = mysql_query("SELECT id, title FROM title");
	while ($row = mysql_fetch_row($q)) $title[$row[0]] = $row[1];
	
	if (!$person[$match]) die("Kunne ikke finde personen...\n</table>\n</table>\n</body>\n</html>");
	
	#print "Forbinder {$person[$mainperson]} med {$person[$match]}...<br /><br />";
	print "Forbinder {$person[$mainperson]} ud i verdenen...<br />";
	print "<small><small><a href=\"/data?person=$match\">mere info om personen</a></small></small><br />";
	
	
	$check[1][] = $mainperson;
	$checked[] = $mainperson;
	$i = 1;
	$personerialt = 1;
	
	// STARTKODE FOR LØKKE
	// running in circles!
	
	
	while($check[$i]) {
		print "<br /><b style=\"color: #c00;\">$i. cirkel:</b><br />";
	
		$inlist = join(",",$check[$i]);	
		$notlist = join(",",$checked);
	
	// Old query
	
		$query_nocon = "
			SELECT
				t2.aut_id AS link,
				sce.id AS sceid,
				sce.title,
				t1.aut_id AS rlink
			FROM
				asrel AS t1,
				sce,
				asrel AS t2,
				aut AS a1,
				aut AS a2
			WHERE
				t1.aut_id IN ($inlist) AND
				sce.id = t1.sce_id AND
				t1.sce_id = t2.sce_id AND
				t2.aut_id NOT IN ($notlist) AND
				t1.aut_id = a1.id AND
				t2.aut_id = a2.id AND
				t1.tit_id = 1 AND t2.tit_id = 1
			GROUP BY
				link
			ORDER BY
				a1.firstname,
				a1.surname,
				a2.firstname,
				a2.surname,
				sce.title
		";
	
	// New query including cons
	
		$query_con = "
			SELECT
				t2.aut_id AS link,
				sce.id AS sceid,
				sce.title,
				t2.tit_id,
				t1.aut_id AS rlink,
				t1.tit_id AS rtit_id,
				convent.name,
				convent.year
			FROM
				asrel AS t1,
				sce,
				asrel AS t2,
				aut AS a1,
				aut AS a2
			LEFT JOIN csrel ON sce.id = csrel.sce_id
			LEFT JOIN convent ON convent.id = csrel.convent_id
			WHERE
				t1.aut_id IN ($inlist) AND
				sce.id = t1.sce_id AND
				t1.sce_id = t2.sce_id AND
				t2.aut_id NOT IN ($notlist) AND
				t1.aut_id = a1.id AND
				t2.aut_id = a2.id AND
				(csrel.pre_id IS NULL OR csrel.pre_id = 1)
			GROUP BY
				link
			ORDER BY
				a1.firstname,
				a1.surname,
				a2.firstname,
				a2.surname,
				t1.tit_id,
				t2.tit_id,
				sce.title
		";
	
	// set query
	
		$query = $query_nocon;
	
		if ($_REQUEST['showquery'] == TRUE) print "<br />$query<br />\n";
		$q = mysql_query($query);
		print mysql_error();
		$qnums++;
		while ($row = mysql_fetch_array($q)) {
			$kobling[$row['link']] = $row['rlink'];
			$personerialt++;
			print "&nbsp;&nbsp;".
			      "<a href=\"{$_SERVER['PHP_SELF']}?from={$row['link']}\" class=\"person\">{$person[$row['link']]}</a> ".
#	 		      "({$title[$row['tit_id']]}) ".
			      "og ".
			      "{$person[$row['rlink']]} ".
#			      "({$title[$row['rtit_id']]}) ".
	# 		      "på <a href=\"data?scenarie={$row['sceid']}\" class=\"scenarie\" style=\"text-decoration: none;\" title=\"{$row['name']} ({$row['year']})\"><em>{$row['title']}</em></a>".
	 		      "på <a href=\"data?scenarie={$row['sceid']}\" class=\"scenarie\" style=\"text-decoration: none;\"><em>{$row['title']}</em></a>".
			      "<br />\n";
			$check[($i+1)][] = $row['link'];
			$checked[] = $row['link'];
		}
		$i++;
	}
	print "&nbsp;&nbsp;Ingen<br />\n";
	
	// SLUTKODE FOR LØKKE
	
	print "<br />\nI alt $qnums database-forespørgsler (eller SQL-queries, om man vil)!<br />\n";
	print "I alt $personerialt personer!<br />";

}

?>

</td></tr></table>
</td></tr></table>

<?
// backtracker
$dotrack = FALSE;
$orig = $match;
$find = 33;
if ($dotrack == TRUE) {
	while ($kobling[$find] != $mach && $a < 10) {
		print $find." til ".$kobling[$find]."<br />";
		$find = $kobling[$find];
		$a++;
	}
}

?>



</body>
</html>
<?
	
#SELECT t1.id, t1.aut_id, t1.sce_id, t2.aut_id, sce.title FROM asrel AS t1, sce, asrel AS t2 WHERE t1.aut_id = 51 AND sce.id = t1.sce_id AND t1.sce_id = t2.sce_id AND t2.aut_id != 51 LIMIT 50;

?>
