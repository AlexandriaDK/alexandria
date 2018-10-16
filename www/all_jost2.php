<?php
require("./connect.php");
require_once("template.inc");

$header = '<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="'.$_SERVER['PHP_SELF'].'" style="text-decoration: none">Six Degrees...</a></i></font>
</p>';
require("page.inc");

$mainperson = intval($_REQUEST['from']);
$subperson = intval($_REQUEST['to']);
$showquery = $_REQUEST["showquery"];

?>
<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=0 align=center><TR><TD BGCOLOR="#C000FB">

<table border=0 cellspacing=0 cellpadding=2 align=center bgcolor="white" width=600>
<tr valign=top><td>
<?php

	print '<form action="'.$_SERVER['PHP_SELF'].'" method=get>';
	$q = mysql_query("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY name");
	print "Vælg person:\n <select name=\"from\">\n";
	while ($r = mysql_fetch_array($q)) {
		print "<option value=\"{$r['id']}\"".($r['id']==$mainperson?" selected":"").">{$r['name']}\n";
	}
	print "</select><br><br>\n";
	print "Vælg person:\n <select name=\"to\">\n";
	mysql_data_seek($q,0);
	while ($r = mysql_fetch_array($q)) {
		print "<option value=\"{$r['id']}\"".($r['id']==$subperson?" selected":"").">{$r['name']}\n";
	}
	print "</select><br><br>\n";
	print "<input type=\"submit\" value=\"Forbind!\">\n</form>\n";
#	print "</td></tr></table>";
#	print "</td></tr></table>";


unset($person);
unset($check);
unset($qnums);

if ($mainperson && $subperson) {

	$q = mysql_query("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut");
	while ($row = mysql_fetch_row($q)) $person[$row[0]] = $row[1];
	
	$q = mysql_query("SELECT id, title FROM title");
	while ($row = mysql_fetch_row($q)) $title[$row[0]] = $row[1];
	
	if (!$person[$mainperson]) die("Kunne ikke finde personen...\n</table>\n</table>\n</body>\n</html>");
	if (!$person[$subperson]) die("Kunne ikke finde personen...\n</table>\n</table>\n</body>\n</html>");
	
	if ($mainperson == $subperson) die("Vælg venligst to <b>forskellige</b> personer...\n</table>\n</table>\n</body>\n</html>");
	
#	print "Forbinder {$person[$mainperson]} med {$person[$subperson]}...<br>";
	#print "Forbinder {$person[$mainperson]} ud i verdenen...<br>";
#	print "<small><small><a href=\"{$_SERVER['PHP_SELF']}\">Forbind nye personer</a></small></small>";
#	print "<br><br>";
	$check[1][] = $subperson;
	$checked[] = $subperson;
	$i = 1;
	$personerialt = 1;
	
	// STARTKODE FOR LØKKE
	// running in circles!
	
	
	while($check[$i]) {
	
		$inlist = join(",",$check[$i]);	
		$notlist = join(",",$checked);
	
	// Old query
	
		$query_nocon = "
			SELECT
				t2.aut_id AS link,
				sce.id AS sceid,
				sce.title,
				t2.tit_id,
				t1.aut_id AS rlink,
				t1.tit_id AS rtit_id
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
				t1.tit_id,
				t2.tit_id,
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
	
		if ($showquery == TRUE) print "<br>$query<br>\n";
		$q = mysql_query($query);
		print mysql_error();
		$qnums++;
		while ($row = mysql_fetch_array($q)) {
			$kobling[$row['link']] = $row['rlink'];
	#		print "($qnums) ".$row['link'] . " => " . $row['rlink']."<br>";
			$scenarie[$row['link']]['title'] = $row['title'];
			$scenarie[$row['link']]['sceid'] = $row['sceid'];
			if ($row['link'] == $mainperson) {
				$found = TRUE;
				break 2;
			}
			$personerialt++;
			$check[($i+1)][] = $row['link'];
			$checked[] = $row['link'];
		}
		$i++;
	}
	
	// SLUTKODE FOR LØKKE
	
	/*
	print "<br>\nI alt $qnums database-forespørgsler (eller SQL-queries, om man vil)!<br>\n";
	print "I alt $personerialt personer!<br>";
	*/
	
	if ($found == TRUE) {
		print $person[$mainperson]." og ".$person[$subperson]." er forbundet i $qnums led:";
	} else {
		print $person[$mainperson]." og ".$person[$subperson]." er ikke forbundet!";
	}
	print "<br><br>\n";
	
	// backtracker
	if ($found == TRUE) {
		$i = 0;
		$find = $mainperson;
		while ($find != $subperson && $i < 20) {
			$i++;
			$scen = $scenarie[$find]['title'];
			$scenid = $scenarie[$find]['sceid'];
			print "$i: <a href=\"/data?person=$find\" class=\"person\">$person[$find]</a> ". 
			      "har lavet ".
			      "<a href=\"/data?scenarie=$scenid\" class=\"scenarie\">$scen</a> ".
			      "med ".
			      "<a href=\"/data?person={$kobling[$find]}\" class=\"person\">{$person[$kobling[$find]]}</a> ". 
			      "<br>\n";
			// til graf
			$graph[] = $find;
			$graph[] = $scenid;
			// næste i rækken
			$find = $kobling[$find];
		}
		// til graf
		$graph[] = $find;
	}

	print "\n</td></tr>\n";
	if ($found == TRUE) {
		print "<tr><td align=\"center\">\n";
		print "<img src=\"jostgraph/sixdegrees_{$mainperson}_{$subperson}.png?".join(',',$graph)."\" />\n";
	#	print "<img src=\"jostgraph/sixdegrees_{$mainperson}_{$subperson}.png?".join(',',$graph)."\" />\n";
	print "</td></tr>\n";
	}
}
?>
			</table>
		</td></tr></table>

	</body>
</html>
<?php
	
#SELECT t1.id, t1.aut_id, t1.sce_id, t2.aut_id, sce.title FROM asrel AS t1, sce, asrel AS t2 WHERE t1.aut_id = 51 AND sce.id = t1.sce_id AND t1.sce_id = t2.sce_id AND t2.aut_id != 51 LIMIT 50;

?>
